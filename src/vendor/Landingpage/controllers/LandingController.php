<?php

namespace Vendor\Landingpage;

class LandingController extends \Vendor\VendorController
{

    /**
     * @var LandingController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var mixed The model instance associated to LandingController
     */
    protected $model;

    /**
     * Returns a LandingController instance, creating it if it did not exist.
     * @return LandingController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->model = LandingModel::singleton();

        /* $pluginFolder must be the same as the Namespace. So... */
        $this->pluginFolder = array_pop(explode('\\', __NAMESPACE__));
    }

    /**
     * Returns the instance of the model for this controller
     * @return LandingModel
     */
    public function getModel()
    {
        return $this->model;
    }

    public function showLandingpage()
    {
        $this
            ->verdor_addStyle('landing.css')
            ->vendor_addScript('landing/landing.js')
            ->vendor_show('landing/index');
    }
}