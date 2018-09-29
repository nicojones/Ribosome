<?php

namespace Vendor\ExampleVendor;

/**
 * Class MenuController - a vendor/added package to the framework.
 * @package Vendor\Menu
 */
class ExampleVendorController extends \Vendor\VendorController {

    /**
     * @var ExampleVendorController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var mixed The model instance associated to MenuController
     */
    protected $model;

    /**
     * Returns a MenuController instance, creating it if it did not exist.
     * @return ExampleVendorController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->model = ExampleVendorModel::singleton();

        /* $pluginFolder must be the same as the Namespace. So... */
        $this->pluginFolder = array_pop(explode('\\', __NAMESPACE__));
    }

    /**
     * Returns the instance of the model for this controller
     * @return ExampleVendorModel
     */
    public function getModel() {
        return $this->model;
    }

    public function showExample() {
        $this
            ->setTitle('ExampleVendorController (from plugin)')
            ->addStyle('example/example.css')
            ->addScript('example/example.js')
            ->vendor_show('exampleView/index');
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