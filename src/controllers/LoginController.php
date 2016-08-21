<?php

    use Core\Providers\Session;
    use Core\Providers\Config;
    use Core\Auth\AuthenticateController as Authenticate;
    use Core\Helpers\Flash;

    /**
     * Shows the login page and logs a user in
     * Class LoginController
     */
    class LoginController extends Authenticate
    {
        /**
         * @var LoginController The class instance.
         * @internal
         */
        protected static $instance;

        /**
         * @var LoginModel The instance of LoginModel.
         */
        protected $model;

        /**
         * Returns a LoginController instance, creating it if it did not exist.
         * @return LoginController
         */
        public static function singleton() {
            if (!self::$instance) {
                $v = __CLASS__;
                self::$instance = new $v;
            }
            return self::$instance;
        }

        /**
         *
         */
        public function __construct() {
            parent::__construct();
            $this->model = LoginModel::singleton();
            $this->auth  = Authenticate::singleton();
        }

        /**
         * Shows the login page / login form
         * @throws Exception
         */
        public function showLogin() {
            if (isAuth()) {
                Flash::set("You're already logged in!", 'info');
                $this->redirect($this->url('LoginHome'));
            }
            $active = 'login';
            $this
                ->setTitle('Login')
                ->add('active', $active)
                ->show('login/login');
        }

        public function doLogin() {
            $user = $this->auth->login();

            if (!$user) {
                $this->redirect($this->url('Login'));
            }
            /**
             * Redirect after login if previous attempt got a 403
             */
            if ($afterLogin = Session::cleanAfterLogin()) {
                $this->redirect($afterLogin);
            } else {
                $this->redirect($this->url('LoginHome'));
            }
        }

        public function showLoginHome() {
            $active = 'login';
            $this
                ->setTitle('Admin Page')
                ->add('active', $active)
                ->show('login/index');
        }
    }