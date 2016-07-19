<?php

    class LicenseModel extends Model
    {

        /**
         * @var LicenseModel The class instance.
         * @internal
         */
        protected static $instance;

        /**
         * Returns a LicenseModel instance, creating it if it did not exist.
         * @return LicenseModel
         */
        public static function singleton() {
            if (!self::$instance) {
                $v = __CLASS__;
                self::$instance = new $v;
            }
            return self::$instance;
        }

        public function loginWithLicense($license, $ip, $userAgent) {
            $query = 'SELECT *, CURRENT_TIMESTAMP as now FROM license WHERE license_key = :license LIMIT 1';
            $res = $this->query($query, [':license' => $license], TRUE);
            if (!$res) {
                return ['error' => "Not a valid license number"];
            } else {
                $licenseInfo = $res[0];
                if (strtotime($licenseInfo['now']) > strtotime($licenseInfo['valid_until'])) {
                    return ['error' => "The license expired on " . date('d M Y', strtotime($licenseInfo['valid_until']))];
                }
                $queryUpdate = 'INSERT INTO license_login (id, license_id, login_time, login_ip, user_agent)
                    VALUES (NULL, :licenseID, CURRENT_TIMESTAMP, :ip, :userAgent)';
                $this->query($queryUpdate, [
                    ':licenseID' => $licenseInfo['id'],
                    ':ip' => $ip,
                    ':userAgent' => $userAgent
                ]);
                return ['user' => $licenseInfo];
            }
        }

        public function addDownload($id, $version, $ip, $userAgent) {
            $queryDownload = 'INSERT INTO license_download (id, license_id, version, downloaded_on, download_ip, user_agent)
              VALUES (NULL, :licenseID, :version, CURRENT_TIMESTAMP, :ip, :userAgent)';
            $this->query($queryDownload, [
                ':licenseID' => $id,
                ':version' => $version,
                ':ip' => $ip,
                ':userAgent' => $userAgent
            ]);

            $this->query('UPDATE license SET times_downloaded = times_downloaded + 1 WHERE id = :id LIMIT 1', [':id' => $id]);
            return true;
        }
    }