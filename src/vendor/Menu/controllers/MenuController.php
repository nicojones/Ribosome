<?php

namespace Vendor\Menu;

class MenuController extends \Vendor\VendorController {

    /**
     * @var MenuController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var mixed The model instance associated to MenuController
     */
    protected $model;

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

    public function __construct() {
        parent::__construct();
        $this->model = MenuModel::singleton();

        /* $pluginFolder must be the same as the Namespace. So... */
        $this->pluginFolder = array_pop(explode('\\', __NAMESPACE__));
    }

    /**
     * Returns the instance of the model for this controller
     * @return MenuModel
     */
    public function getModel() {
        return $this->model;
    }

    public function showMenu() {
        $this
            ->setTitle('MenuController (from plugin)')
            ->addStyle('menu/menu.css')
            ->addScript('menu/menu.js')
            ->vendor_show('menu/index');
    }
    
    public function getMenu($active = 'home') {
        $menu = $this->model->getMenu();
        return $this->vendor_get('html/blocks/header', array('active' => $active, 'menu' => $menu));
    }
    
    public function getFooter($active = 'home') {
        $footer = $this->model->getFooter();
        return $this->vendor_get('html/blocks/footer', array('active' => $active, 'footer' => $footer));
    }
}