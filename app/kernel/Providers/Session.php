<?php

namespace Core\Providers;

/**
 * Manages the <b>$_SESSION</b> global with getters, setters and destructors.
 *
 * @usage Please see {@see \Core\Session::__callStatic}
 *
 * @package Core
 */
class Session
{
    protected static $roleGuest = __ROLE_GUEST__;
    protected static $roleUser = __ROLE_USER__;
    protected static $roleAdmin = __ROLE_ADMIN__;
    
    /**
     * Sets a <b>$_SESSION[$key] = $value</b>. Do not use directly.
     * @see Session::__callStatic
     * @param string $key The key where to save it
     * @param mixed $value The value to save
     * @return mixed The value set
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
        return $value;
    }

    /**
     * Gets the value of a <b>$_SESSION</b> key. Do not use directly.
     * @see Session::__callStatic
     * @param string $key
     * @return mixed <b>$_SESSION[key]</b> if it exists, <b>NULL</b> otherwise
     */
    public static function get($key = NULL) {
        if (is_null($key)) {
            return $_SESSION;
        } elseif (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return NULL;
    }
    
    /**
     * Gets <b>$_SESSION[$key][$subKey]</b> if exists, <b>$_SESSION[$key]</b> otherwise or <b>NULL</b>. Do not use directly.
     * @see Session::__callStatic
     * @param string $key The key where it's set
     * @param string $subKey The subkey where it's set
     * @return mixed SESSION[key][subKey] if exists or NULL if it doesn't
     */
    public static function getExt($key, $subKey) {
        if (isset($_SESSION[$key])) {
            if(isset($_SESSION[$key][$subKey])) {
                return $_SESSION[$key][$subKey];
            }
            return $_SESSION[$key];
        }
        return NULL;
    }
    
    /**
     * Cleans a session parameter if $key is specified, or cleans the whole <b>$_SESSION</b> otherwise. Do not use directly.
     * @see Session::__callStatic
     * @param string $key = NULL. If not specified, sets <b>$_SESSION = NULL</b>; otherwise, unsets <b>$_SESSION[key]</b>
     * @return null
     */
    public static function clean($key = NULL) {
        $value = NULL;
        
        if (is_null($key)) {
            $value = $_SESSION;
            $_SESSION = NULL;
            unset($_SESSION);
        } elseif (!empty($_SESSION[$key])) {
            $value = $_SESSION[$key];
            $_SESSION[$key] = NULL;
            unset($_SESSION[$key]);
        } else {
            // nothing; we return null
        }
        return $value;
    }
    
    /**
     * Returns whether the user is authenticated or not
     * @return bool Whether the user is logged in or not (i.e. whether is user|TRUE or guest|FALSE)
     */
    public static function isUser() {
        return self::get('role') >= self::$roleGuest;
    }
    
    /**
     * Returns whether the user is superuser (<b>admin</b>) or not
     * @return bool Whether the user is sudo (<b>role = __ROLE_ADMIN__</b>) or not
     */
    public static function isAdmin() {
        return self::get('role') == self::$roleAdmin;
    }
    
    /**
     * Returns whether the user is authenticated or not. If it is, returns its role (<b>__ROLE_&lt;role&gt;__</b>)
     * @return mixed FALSE if not authenticated, $role (!= 0) if authenticated
     */
    public static function isAuthenticated() {
        $role = self::get('role');
        if ($role > self::$roleGuest) {
            return $role;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Manages all getters, setters and destroyers for Session. Use <b>getFoo()</b>, <b>getFoo('bar')</b> or <b>cleanFoo()</b>.
     * See example for more details and how to use.
     * @param string $methodName the method to perform. Ex: setUser, getCart
     * @param mixed $args the value
     * @example
     * Set a variable (any type) with:
     * <code>Session::setFoo($fooValue)</code>
     * To read it, use <code>$foo = Session::getFoo()</code>
     * To delete a variable, use <code>$foo = Session::cleanFoo()</code>
     * (Cleaning a variable returns its value so it can be used as a flash var.)
     * @return mixed $args
     */
    public static function __callStatic($methodName, $args) {
        if (preg_match('/^(set|get|clean|is)([A-Z])(.*)$/', $methodName, $matches)) {
            $property = strtolower($matches[2]) . $matches[3];

            switch ($matches[1]) {
                case 'set':
                    return self::set($property, $args[0]);
                case 'get':
                    if (!count($args)) {
                        return self::get($property);
                    } else {
                        return self::getExt($property, $args[0]);
                    }
                case 'clean':
                    self::clean($property);
                    break;
                default:
                    break;
            }
            return $args;
        } else {
            return NULL;
        }
    }
}