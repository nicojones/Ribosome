<?php

namespace Vendor\Landingpage;

class LandingModel extends \Model {

    /**
     * @var LandingModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a LandingModel instance, creating it if it did not exist.
     * @return LandingModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }
}