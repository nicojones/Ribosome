<?php

class MenuController extends Controller
{

    /**
     * @var MenuController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a MenuController instance, creating it if it did not exist.
     * @return MenuController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function showMenu() {
        $this->setTitle('MenuController (from /src)')->show('menu/index');
    }
}