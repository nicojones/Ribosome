<?php

    use Core\Session;

    class LicenseController extends Controller
    {

        /**
         * @var LicenseController The class instance.
         * @internal
         */
        protected static $instance;

        /**
         * @var mixed The model instance associated to LicenseController
         */
        protected $model;

        /**
         * Returns a LicenseController instance, creating it if it did not exist.
         * @return LicenseController
         */
        public static function singleton() {
            if (!self::$instance) {
                $v = __CLASS__;
                self::$instance = new $v;
            }
            return self::$instance;
        }

        public function __construct() {
            parent::__construct();
            $this->model = LicenseModel::singleton();
        }

        /**
         * Returns the instance of the model for this controller
         * @return LicenseModel
         */
        public function getModel() {
            return $this->model;
        }

        public function showLogin() {
            $this->show('license/login');
        }

        public function login() {
            $license = $this->getPost('license');
            $ip = getClientIP();
            $userAgent = getClientUserAgent();

            $licenseUser = $this->model->loginWithLicense($license, $ip, $userAgent);
            if (!empty($licenseUser['user'])) {
                Session::setLicenseUser($licenseUser['user']);
                $this->redirect($this->url('LicenseHome'));
            } else {
                Session::setAlert(['type' => 'danger', 'message' => $licenseUser['error']]);
                $this->redirect($this->url('LicenseLogin'));
            }
        }

        public function showHome() {
            $this
                ->add('license', Session::getLicenseUser())
                ->show('license/index');
        }

        public function showDocs() {
            // $version = $this->getGet('version'); // we don't use it; instead we get it from session
            $version = Session::getLicenseUser('version_current');
            $file = explode('?', $this->getGet('file'));
            die(file_get_contents(__ROOT__ . '/src/views/html/docs/' . $version . '/' . $file[0]));
        }

        public function showDocsAsset() {
            // $version = $this->getGet('version'); // we don't use it; instead we get it from session
            $version = Session::getLicenseUser('version_current');
            $folder = $this->getGet('folder');
            $file = explode('?', $this->getGet('file'));
            list($dummy, $format) = explode('.', $file[0]);
            $extension = [
                'css' => 'text/css',
                'js' => 'text/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpg',
                'gif' => 'image/gif'
            ];
            header('Content-type: ' . $extension[$format]);
            readfile(__ROOT__ . '/src/views/html/docs/' . $version . '/' . $folder . '/' . $file[0]);
        }

        public function downloadFramework() {
            $this->model->addDownload(Session::getLicenseUser('id'), Session::getLicenseUser('version_current'), getClientIP(), getClientUserAgent());
            header('Content-Disposition: attachment; filename=Framework-v' . Session::getLicenseUser('version_current') . '.zip');
            readfile(__ROOT__ . '/src/views/html/docs/' . Session::getLicenseUser('version_current') . '/Framework.zip');
        }

        public function logout() {
            Session::cleanLicenseUser();
            $this->redirect($this->url('LicenseLogin'));
        }
    }