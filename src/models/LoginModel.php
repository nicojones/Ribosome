<?php

class LoginModel extends \Core\LoginModel
{

    /**
     * @var LoginModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a LoginModel instance, creating it if it did not exist.
     * @return LoginModel
     */
    public static function singleton()
    {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }
}
