<?php

/**
 * APIModel goes great with APIController
 */
class APIModel extends Model
 {
 
    /**
     * @var APIModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a APIModel instance, creating it if it did not exist.
     * @return APIModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function createTracker($tName, $hash, $secret, $ip, $active) {
        $query = '
            INSERT INTO tracker 
            (`tname`, `hash`, `secret`, `ip`, `added_on`, `active`)
            VALUES
            (:tName, :hash, :secret, :ip, CURRENT_TIMESTAMP, :active)';

        $inserted = $this->query($query, [
            'tName' => $tName,
            'hash' => $hash,
            'secret' => $secret,
            'ip' => $ip,
            'active' => $active
        ]);

        return $this->db->lastInsertId();
    }
}