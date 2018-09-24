<?php

namespace Core\Auth;
use Kernel\Providers\Permission;
use Core\ParentController;
use Core\Providers\Session;

/**
 * Class AuthenticateController contains functions for user (de)authentication.
 *
 * @hooks
 * <code>
 * ('on_userauth', ['user' => $user]) // Called just after the login session has been created {@see \Core\AuthenticateController::login}
 * ('on_userdeauth', ['user' => Session::getUser()]) // Called just before destroying the login session. {@see \Core\AuthenticateController::deauthenticateUser}
 * </code>
 * @package Core
 */
class AuthenticateController extends ParentController {

    /**
     * @var AuthenticateController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var PasswordModel The instance of the model
     */
    protected $model;

    /**
     * @var \Kernel\Providers\Permission The instance of the Permission class. Used for (de)authentication.
     */
    protected $permission;

    public function __construct() {
        parent::__construct();
        $this->model = PasswordModel::singleton();
        $this->permission = Permission::singleton();
    }

    /**
     * Returns a AuthenticateController instance, creating it if it did not exist.
     * @return AuthenticateController
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
     * @return PasswordModel
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Performs an authentication request for a user with 'username' and 'password' post vars
     * If the credentials are not correct, AuthenticateController->authenticateUser throws the user accordingly
     *
     * @param string $username (Optional) if $username is
     * @param string $password
     *
     * @return array $user The authenticated user (on success) or FALSE on fail
     */
    public function login($username = '', $password = '') {
        $user = $this->authenticateUser(
            $username ?: $this->getPost('username'),
            $password ?: $this->getPost('password')
        );
        if ($user) {
            Session::setUser($user);

            $this->hooks->do_action('on_userauth', ['user' => $user]);
        }

        // return either the $user array or false.
        return $user;
    }

    /**
     * Logs a user out. That is, sets the current user with ROLE_USER privileges and redirects them to the Home path.
     * @throws \Exception if the path 'Home' doesn't have a key in routing.ini
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
             * If user didn't authenticate properly, we return false
             */
            return false;
            //$this->redirect($this->url('Login'));
        }
        $this->permission->setUserPermission($user['role']);
        return $user;
    }

    /**
     * Deauthenticates the current user by setting ROLE_USER privileges.
     * @return AuthenticateController;
     */
    protected function deauthenticateUser() {
        $this->hooks->do_action('on_userdeauth', ['user' => Session::getUser()]);

        Session::cleanUser();
        $this->permission->setUserPermission(__ROLE_GUEST__);
        return $this;
    }
}