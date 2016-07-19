<?php

namespace Kernel;
use Core\Config;
use Core\Session;

/**
 * Manages the permissions required for a specific call (<b>Controller->action()</b>).<br/>
 * If the user has not enough permissions, it returns a <b>__throw_to</b> action to redirect them to.
 *
 * @usage
 * Enter the name of the Controller (but without writing "Controller"; e.g., for HomeController, write [Home].<br/>
 * There is no ambiguity here, for there should be no direct access/permission to Model files.<br/>
 *
 * By default, &lt;Controller&gt;@&lt;action&gt; has permission level = 1 for all controllers and actions.<br/>
 * That means, if you don't write it down there'll be no errors thrown, but also no security level!<br/>
 *
 * @example
 * <code>
 * ; For LoginController, it should look something like
 * [Login]
 *     __throw_to = "Login" ; any unauthorized request goes to the Login path (check routing.ini)
 *      * = 2               ; As a general rule, in this example all calls require being authenticated
 *     showLogin = 1        ; Of course, to log in you must be guest, so this has level=1
 *     login = 1 ; user     ; On submitting the login form, you are not authenticated yet! level=1
 *     showLoginHome = 2    ; Once logged in, you go to the "admin zone": level=2
 * </code>
 *
 * @package Kernel
 */
class Permission {

    /**
     * @var Permission|false $instance A Permission instance
     * @internal
     */
    private static $instance;

    /**
     * @var Config The instance of the Config class
     */
    private $config;

    private function __construct() {
        $this->config = Config::singleton();
    }

    /**
     * Returns a Permission instance, creating it if it did not exist
     * @return Permission
     */
    public static function singleton() {
        if (!self::$instance) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    /**
     * Returns whether the user has permission to acces a given method
     * @param string $controller The controller name
     * @param string $method The method name
     * @return bool $accesAllowed;
     */
    public function checkPermission($controller, $method) {
        $class = $this->config->get('Permissions', $controller);
        $classPermission = $this->getPermission($class, $method);
        $userPermission = $this->getUserPermission();
        if ($classPermission <= $userPermission) {
            return ['allowed' => TRUE];
        } else {
            $afterLogin = isset($class['after_login']) ? (bool)(int)$class['after_login'] : FALSE;
            return [
                'allowed' => FALSE,
                'throw_to' => $class['__throw_to'],
                'after_login' => $afterLogin
            ];
        }
    }

    /**
     * Returns the user permission as an integer.
     * @return int user permission
     */
    public function getUserPermission() {
        $role = Session::getRole();
        return $role ?: Session::setRole(__ROLE_GUEST__);
    }

    /**
     * Sets a permission level to the current user
     * @param int $level = 1 The permission level
     * @return Permission
     */
    public function setUserPermission($level = 1) {
        Session::setRole($level);
        return self::$instance;
    }
    
    /**
     * Gets the user permission for a given class and method
     * @param array $class
     * @param string $method
     * @return int ROLE level
     */
    private function getPermission($class, $method) {
        if (isset($class[$method])) {
            return $class[$method];
        } elseif (isset($class['*'])) {
            return $class['*'];
        } else {
            return __ROLE_GUEST__;
        }
    }
}