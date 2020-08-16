<?php


/**
 * APIController seems a cool idea
 */
class APIController extends Controller
{
 
    /**
     * @var APIController The class instance.
     * @internal
     */
    protected static $instance;

    protected $model;

    /**
     * Returns a APIController instance, creating it if it did not exist.
     * @return APIController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * Constructor for the class APIController
     */
    public function __construct() {
        parent::__construct();

        $this->model = APIModel::singleton();
    }

    public function loadConfig() {
        $this->header(200);
        $config = [
            'title' => 'Email Tracker',
            'url' => [
                'image'               => $this->url('Image', [':hash' => ':hash']),
                'newTracker'          => $this->url('NewTracker'),
                'tracker'             => $this->url('Tracker', [':secret' => ':secret']),
                'trackerGetVisits'    => $this->url('TrackerGetVisits', [':secret' => ':secret']),
                'trackerList'         => $this->url('TrackerList', [':user_secret' => ':user_secret']),
                'trackerSearch'       => $this->url('TrackerSearch'),
                'trackerChangeName'   => $this->url('TrackerChangeName'),
                'trackerChangeUSecret'=> $this->url('TrackerChangeUSecret'),
                'trackerSetState'     => $this->url('TrackerSetState'),
            ]
        ];

        die(json_encode($config));
    }

    public function createNewTracker()
    {
        $this->header(200);
        $request = $this->getJSON();

        $tName = $request['tname'] ?: 'no_name';
        $uniqueHash = $this->getUniqueTrackerHash(10);
        $secret = $this->getUniqueTrackerSecret(10);
        $ip = $_SERVER['REMOTE_ADDR'];
        $active = 0;

        $trackerID = $this->model->createTracker($tName, $uniqueHash, $secret, $ip, $active);

        $tracker = $this->getTracker($uniqueHash);


        die(json_encode( $tracker ));
    }

    public function fetchTracker()
    {
        $this->header(200);

        $secret = $this->getGet('secret', 'no-secret');

        $tracker = $this->getTrackerBySecret($secret, true);

        $ip = $_SERVER['REMOTE_ADDR'];
        $this->model->updateRow('tracker', 'ip', $tracker['id'], $ip);
        unset($tracker['id']);

        $this->model->addTrackerField($tracker, 'user_secret');
        //$tracker && $this->saveTrackerToSession($tracker);

        die(json_encode($tracker));
    }

    public function fetchTrackersByUserSecret()
    {
        $this->header(200);

        $userSecret = $this->getGet('user_secret', 'no-secret');

        $trackers = $this->getTrackersByUserSecret($userSecret, false);

        $ip = $_SERVER['REMOTE_ADDR'];
        $this->model->query('UPDATE tracker SET ip = :ip WHERE user_secret = :userSecret',
            ['ip' => $ip, 'userSecret' => $userSecret]);

        die(json_encode($trackers));
    }

    public function setState()
    {
        $this->header(200);
        $request = $this->getJSON();

        $secret = $request['secret'];

        $tracker = $this->getTrackerBySecret($secret, true);

        if (!$tracker)
        {
            die(json_encode(new stdClass()));
        }

        $setToState = $request['active'];
        $tracker['active'] = ($setToState ? $setToState : !(boolean)$tracker['active']);

        $this->model->updateRow('tracker', 'active', $tracker['id'], $tracker['active']);
        $this->model->addTrackerField($tracker, 'user_secret');

        die(json_encode($tracker));
    }

    public function changeTrackerName() {
        $this->header(200);
        $request = $this->getJSON();

        $secret = $request['secret'];

        $tracker = $this->getTrackerBySecret($secret, true);

        if ($tracker)
        {
            $tname = $request['tname'];
            $this->model->updateRow('tracker', 'tname', $tracker['id'], $tname);
            $tracker['tname'] = $tname;
            $this->model->addTrackerField($tracker, 'user_secret');
        }

        die(json_encode($tracker));
    }

    public function changeTrackerUserSecret() {
        $this->header(200);
        $request = $this->getJSON();

        $secret = $request['secret'];

        $tracker = $this->getTrackerBySecret($secret, true);

        if ($tracker)
        {
            $userSecret = $request['user_secret'];
            $this->model->updateRow('tracker', 'user_secret', $tracker['id'], $userSecret);
            $tracker['user_secret'] = $userSecret;
            $this->model->addTrackerField($tracker, 'user_secret');
        }

        die(json_encode($tracker));
    }

    public function getTrackerVisits() {
        $this->header(200);
        $secret = $this->getGet('secret');

        if (!$tracker = $this->getTrackerBySecret($secret, true)) {
            die(json_encode([]));
        }

        $visits = $this->model->getVisits($tracker['id']);
        foreach ($visits as &$v) {
            $coords = $v['lat'] . ',' . $v['lng'];
            $v['map'] = $map = $this->staticMapURL . '&size=640x280&center=' . $coords . '&zoom=13&markers=' . $coords;
        }

        die(json_encode($visits));
    }

    public function searchTracker() {

        $this->header(200);
        $query = $this->getGet('q');

        $tracker = $this->getTrackerBySecret($query, false);
        $results = [];
        if ($tracker) {
            $this->model->addTrackerField($tracker, 'user_secret');
            $tracker['search_type'] = 'secret';
            $results[] = $tracker;
        }

        $trackersByUserSecret = $this->model->getTrackersByUserSecret($query);
        if (count($trackersByUserSecret)) {
            $trackersList = $trackersByUserSecret[0];
            $trackersList['search_type'] = 'list';
            $results[] = $trackersList;
            foreach ($trackersByUserSecret as &$tbus) {
                $tbus['search_type'] = 'secret';
                $tbus['active'] = (boolean)$tbus['active'];
                $tbus['visits'] = (int)$tbus['visits'];
            }
            $results = array_merge($results, $trackersByUserSecret);
        }

        die(json_encode($results));
    }

    /**
     * Returns a unique base64-hash (using ::generateHash()), i.e. that has not been used by any tracker hash.
     * @param int $length The length of the string
     *
     * @return string Unique hash
     */
    public function getUniqueTrackerHash($length = 5) {
         do {
             $hash = $this->generateHash($length);
             $exists = (bool)$this->model->getTrackerByHash($hash);
         } while ($exists);
         return $hash;
    }

    /**
     * Returns a unique base64-hash (using ::generateHash()), i.e. that has not been used by any tracker secret.
     * @param int $length The length of the string
     *
     * @return string Unique hash
     */
    public function getUniqueTrackerSecret($length = 10) {
         do {
             $hash = $this->generateHash($length);
             $exists = (bool)$this->model->getTrackerBySecret($hash);
         } while ($exists);
         return $hash;
    }
}