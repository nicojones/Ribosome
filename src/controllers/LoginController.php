<?php

use Core\Session;
use Core\Response;

class LoginController extends \Core\LoginController
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

    public function __construct() {
        parent::__construct();
        $this->model = LoginModel::singleton();
    }

    public function showLogin() {
        if (Session::isAuthenticated()) {
            \Core\Flash::set("You're already logged in!", 'info');
            $this->redirect($this->url('LoginHome'));
        }
        $active = 'login';
        $this
            ->setTitle('Login')
            ->add('active', $active)
            ->show('login/login');
    }

    public function showLoginHome() {
        $active = 'login';
        $this
            ->setTitle('Admin Page')
            ->add('active', $active)
            ->show('login/index');
    }
}