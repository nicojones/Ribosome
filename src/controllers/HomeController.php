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

    public function mainPage() {
        $this
            ->show('home/index');
    }

    public function image() {
        $this->header(200);

        $hash = $this->getGet('hash', "not-found");

        if ($tracker = $this->getTracker($hash, false, true))
        {
            $trackerIP = ($this->model->getRow('tracker',  $tracker['id'], 'id'))['ip'];

            if ((int)$tracker['active'] !== 0 && ($trackerIP !== $_SERVER['REMOTE_ADDR']))
            {
                // add tracking info.
                $ip = $_SERVER['REMOTE_ADDR'];
                $address = $this->getLocationFromIP($ip);
                $visitID = $this->model->insertVisit($tracker['id'], $ip, $address['address'], $address['lat'], $address['lng'], $address['full']);
            }
            else {
                // tracker is NOT active -> we don't save visits.
            }
        }
        else {
            // tracker doesn't exist.
        }

        header("Content-type: image/jpeg");
        $image = imagecreatefrompng(__ROOT__ . '/public/images/icons/1px.png');
        imagejpeg($image);
        die();
    }
}