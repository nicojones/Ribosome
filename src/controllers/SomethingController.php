<?php

class SomethingController extends Controller
 {
 
    /**
     * @var SomethingController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a SomethingController instance, creating it if it did not exist.
     * @return SomethingController
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
    }
}