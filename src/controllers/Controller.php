<?php

    use \Core\Providers\Session;

/**
 * Class Controller acts as a stepping stone (or "intermediate agent") between the user-defined code (<b>/src/controllers/</b>)
 * and the system-defined code (<b>ParentController</b>) that helps to better structure the code. See &#64;example
 * @example
 * <code>
 * // To avoid:
 * FooController->showProducts();
 * // and
 * BarController->showProducts();
 * // to be defined twice (one in each controller) or once (in ParentController, bad code practices)
 * Controller->showProducts();
 * // can be defined and thus accessed from both <b>Foo</b> and <b>Bar</b> Controllers.
 * </code>
 */
class Controller extends \Core\ParentController
{

    /**
     * @var Controller The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var Model The instance of Model.
     */
    protected $model;

    protected $staticMapURL = 'https://maps.googleapis.com/maps/api/staticmap?key=' . __GAPIKEY__;

    /**
     * Returns a Controller instance, creating it if it did not exist.
     * @return Controller
     */
    public static function singleton()
    {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function __construct() {
        parent::__construct();
        $this->model = Model::singleton();
    }


    public function getTracker($hash, $secret = false, $returnID = false)
    {
        $tracker = $this->model->getTrackerByHash($hash);

        if (empty($tracker) || (!empty($secret) && $tracker['secret'] !== $secret)) {
            return [];
        }
        // else
        // we know the tracker exists, and if we passed a $secret we know it matches.

        $tracker['src'] = $this->getTrackerSrc($tracker['hash']);
        $tracker['active'] = (bool)(int)$tracker['active'];

        if (!$returnID) {
            unset($tracker['id']);
        }

        return $tracker;
    }

    public function getTrackerBySecret($secret, $returnID = false)
    {
        $tracker = $this->model->getTrackerBySecret($secret);

        if (empty($tracker)) {
            return NULL;
        }
        // else
        // we know the tracker exists, and if we passed a $secret we know it matches.

        $tracker['src'] = $this->getTrackerSrc($tracker['hash']);
        $tracker['active'] = (bool)(int)$tracker['active'];

        if (!$returnID) {
            unset($tracker['id']);
        }

        return $tracker;
    }

    public function getTrackersByUserSecret($userSecret, $returnID = false)
    {
        $trackers = $this->model->getTrackersByUserSecret($userSecret);

        if (!count($trackers)) {
            return [];
        }
        // else
        // we know the tracker exists, and if we passed a $secret we know it matches.

        foreach ($trackers as &$tracker) {
            $tracker['src'] = $this->getTrackerSrc($tracker['hash']);
            $tracker['active'] = (bool)(int)$tracker['active'];
            if (!$returnID) {
                unset($tracker['id']);
            }
        }


        return $trackers;
    }

    public function getTrackerSrc($hash) {
        return __SITE_URL__ . $this->url('Image', [':hash' => $hash]);
    }

    /**
     * Generates a base-64 hash of length $length
     * @param int $length The length for the  hash
     *
     * @return string A base-64, youtube-like hash
     */
    protected function generateHash($length = 11, $lowercase = false) {
        $validCharacters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // base62
        if ($lowercase) {
            $validCharacters .= 'abcdefghijklmnopqrstuvwxyz';
        }
        //$validCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_'; // base64

        $validCharNumber = strlen($validCharacters);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $result .= $validCharacters[$index];
        }

        return $result;
    }

    public function getJSON() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata, TRUE);

        if ($request === NULL) {
            die();
        }

        return $request;
    }

//    public function saveTrackerToSession($tracker)
//    {
//        var_dump(Session::getTrackers(), $tracker);
//        if (empty(Session::getTrackers()))
//        {
//            Session::setTrackers([]);
//        }
//        if (!empty($tracker) && $tracker['hash'])
//        {
//            $trackers = Session::getTrackers();
//            $trackers[$tracker['hash']] = $tracker['hash'];
//            Session::setTrackers($trackers);
//        }
//    }
//
//    public function hasTrackerFromSession($tracker)
//    {
//        if (empty(Session::getTrackers()))
//        {
//            return false;
//        }
//        return in_array($tracker['hash'], Session::getTrackers());
//    }

    public function getLocationFromIP($ip) {
        $res = file_get_contents('http://ip-api.com/json/' . $ip);
        $address = [];
        $json = [];

        try {
            $json = json_decode($res, TRUE);
        } catch (Error $e) {
            return [];
        }

        $address = [
            'address' => $json['city'] . ', ' . $json['regionName'] . ', ' . $json['country'],
            'lat' => $json['lat'],
            'lng' => $json['lon'],
            'full' => (string)$res
        ];

        return $address;
    }
}
