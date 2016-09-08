<?php

/**
 * The HomeController is a user-defined controller. Should be in charge of managing the home.
 */
class HomeController extends Controller {

    /**
     * @var HomeController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var HomeModel The instance of HomeModel.
     */
    protected $model;

    /**
     * Returns a HomeController instance, creating it if it did not exist.
     *
     * @return HomeController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * The __constructor for the class
     * Instantiates the HomeModel
     */
    public function __construct() {
        parent::__construct();
        $this->model = HomeModel::singleton();
    }

    /**
     * Returns the instance of the model for this controller
     *
     * @return \HomeModel
     */
    public function getModel() {
        return $this->model;
    }

    public function showHome() {
//        $this->hooks->add_action('extra_params_path', function($params) {
            // I'll appear on the "Login" link on the home page
//            return ['im_a_hook' => 'yes'];
//        });

        $this->hooks->add_action('exec_afterend', function($params) {
            echo "<small style='position:fixed;bottom:0'>I'm a hook called from " .
                $params['controller'] . '->' . $params['action'] . '()</small>';
        });
        $this->show('home/index');

        return $this;
    }

    /*
     * See more info about this function at /src/config/routing.ini :)
     */
    public function animalZoo() {
        die($_GET['animal'] . ' -> ' . $_GET['sound'] . ' -> ' . $_GET['extra']);
    }
}