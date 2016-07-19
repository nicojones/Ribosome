<?php

class HomeModel extends Model
{

    /**
     * @var HomeModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a HomeModel instance, creating it if it did not exist.
     * @return HomeModel
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
