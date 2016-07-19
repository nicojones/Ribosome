<?php

namespace Core;
use Kernel\Permission;

/**
 * Class LoginController contains functions for user (de)authentication.
 *
 * @hooks
 * <code>
 * ('on_userauth', ['user' => $user]) // Called just after the login session has been created {@see \Core\LoginController::login}
 * ('on_userdeauth', ['user' => Session::getUser()]) // Called just before destroying the login session. {@see \Core\LoginController::deauthenticateUser}
 * </code>
 * @package Core
 */
class LoginController extends ParentController {

    /**
     * @var LoginController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var LoginModel The instance of the model
     */
    protected $model;

    /**
     * @var \Kernel\Permission The instance of the Permission class. Used for (de)authentication.
     */
    protected $permission;

    public function __construct() {
        parent::__construct();
        $this->model = LoginModel::singleton();
        $this->permission = Permission::singleton();
    }

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
     * Returns the instance of the model for this controller
     * @return LoginModel
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Performs an authentication request for a user with 'username' and 'password' post vars
     * If the credentials are not correct, LoginController->authenticateUser throws the user accordingly
     */
    public function login() {
        $user = $this->authenticateUser($this->getPost('username'), $this->getPost('password'));
        Session::setUser($user);
        $this->hooks->do_action('on_userauth', ['user' => $user]);

        /**
         * Redirect after login if previous attempt got a 403
         */
        if ($afterLogin = Session::cleanAfterLogin()) {
            $this->redirect($afterLogin);
        } else {
            $this->redirect($this->url('LoginHome'));
        }
    }

    /**
     * Logs a user out. That is, sets the current user with ROLE_USER privileges and redirects them to the Home path.
     */
    public function logout() {
        $this->deauthenticateUser();
        $this->redirect($this->url('Home'));
    }

    /**
     * Checks against the database or the config file (depending on configuration) if the credentials are correct.
     * @param string $username
     * @param string $password
     * @return mixed $user on success. On fail redirects accordingly.
     */
    protected function authenticateUser($username, $password) {
        $config = $this->config->get('Login');
        $dbEnabled = (bool)(int)$this->config->get('Database', 'DB_SUPPORT');
        $user = [];
        if ($dbEnabled && $config['DB_SUPPORT'] == 1) {
            $user = $this->model->getUser($username, $password);
        } elseif ($username == $config['LOGIN_USERNAME'] && $password == $config['LOGIN_PASSWORD']) {
            $user = array(
                'username' => $username,
                'password' => $password,
                'role' => __ROLE_ADMIN__); /* only one user? must be an admin! */
        } else {
            // Not valid credentials... -> !$user = true;
        }
        if (!$user) {
            /**
             * If user didn't authenticate properly, we redirect to Login
             */
            $this->redirect($this->url('Login'));
        }
        $this->permission->setUserPermission($user['role']);
        return $user;
    }

    /**
     * Deauthenticates the current user by setting ROLE_USER privileges.
     * @return LoginController;
     */
    protected function deauthenticateUser() {
        $this->hooks->do_action('on_userdeauth', ['user' => Session::getUser()]);

        Session::cleanUser();
        $this->permission->setUserPermission(__ROLE_GUEST__);
        return $this;
    }
}