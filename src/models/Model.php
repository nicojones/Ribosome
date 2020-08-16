<?php

/**
 * Class Model acts as a stepping stone (or "intermediate agent") between the user-defined code (<b>/src/models/</b>)
 * and the system-defined code (<b>ParentModel</b>) that helps to better structure the code. See &#64;example
 * @example
 * <code>
 * // To avoid:
 * FooController->queryProducts();
 * // and
 * BarController->queryProducts();
 * // to be defined twice (one in each controller) or once (in ParentController, bad code practices)
 * Controller->queryProducts();
 * // can be defined and thus accessed from both <b>Foo</b> and <b>Bar</b> Controllers.
 * </code>
 */
class Model extends \Core\ParentModel
{

    /**
     * @var Model The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a Model instance, creating it if it did not exist.
     * @return Model
     */
    public static function singleton()
    {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function getTrackerByHash($hash)
    {
        $query = 'SELECT t.* from tracker_with_visit t where t.hash = :hash LIMIT 1';
        $result = $this->query($query, ['hash' => $hash], TRUE);

        return $result ? $result[0] : false;
    }

    public function getTrackerBySecret($secret)
    {
        $query = 'SELECT t.* from tracker_with_visit t where t.secret = :secret LIMIT 1';
        $result = $this->query($query, ['secret' => $secret], TRUE);

        return $result ? $result[0] : false;
    }

    /**
     * Adds a certain column value to the field.
     * @param $tracker
     * @param string|'user_secret' $field
     *
     * @return mixed
     */
    public function addTrackerField(&$tracker, $field = 'user_secret') {
        $query = "SELECT `$field` FROM tracker WHERE tracker.hash = :hash LIMIT 1";
        $result = $this->query($query, ['hash' => $tracker['hash']], TRUE);

        if ($result) {
            $tracker[$field] = $result[0][$field];
        }

        return $tracker; // although not needed
    }

    public function getTrackersByUserSecret($userSecret) {
        $query = 'SELECT t.* from tracker_with_visit t where t.user_secret = :userSecret';
        $result = $this->query($query, ['userSecret' => $userSecret], TRUE);

        return $result;
    }

    public function insertVisit($trackerID, $ip, $address, $lat, $lng, $full) {
        $query = 'INSERT INTO visit (tracker_id, ip, added_on, address, lat, lng, full)
                  VALUES (:trackerID, :ip, CURRENT_TIMESTAMP, :address, :lat, :lng, :full)';
        $inserted = $this->query($query, [
            'trackerID' => $trackerID,
            'ip' => $ip,
            'address' => $address,
            'lat' => $lat,
            'lng' => $lng,
            'full' => $full
        ]);

        return $this->db->lastInsertId();
    }

    public function getVisits($id) {
        $query = 'SELECT ip, added_on, address, lat, lng FROM visit WHERE visit.tracker_id = :id ORDER BY added_on DESC';

        return $this->query($query, ['id' => $id], TRUE);
    }
}
