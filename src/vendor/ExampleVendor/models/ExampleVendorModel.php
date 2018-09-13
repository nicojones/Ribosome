<?php

namespace Vendor\ExampleVendor;

class ExampleVendorModel extends \Model {

    /**
     * @var ExampleVendorModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a MenuModel instance, creating it if it did not exist.
     * @return ExampleVendorModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }
    
    public function getMenu() {
        return "I'm a stupid menu";
    }
    
    public function getFooter() {
        return "Footer :)";
    }
}