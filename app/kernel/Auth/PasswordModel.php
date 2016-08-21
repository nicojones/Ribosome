<?php

namespace Core\Auth;
use Core\ParentModel;

/**
 * Contains the default logic to retrieve a user from the database.
 * @package Core
 */
class PasswordModel extends ParentModel {

    /**
     * @var PasswordModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns the class instance, creating it if it did not exist.
     * @return PasswordModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * Gets an user from the database and updates last_login and attempts
     * @param string $username The name of the user
     * @param string $password The <b>unhashed</b> password of the user
     * @return mixed $user on success, FALSE on error
     */
    public function getUser($username, $password) {
        $db = $this->config->get('Login');
        $loginTable = $db['TABLE'];
        $usernameRow = $db['TABLE_COLUMN_USER'];
        $passwordRow = $db['TABLE_COLUMN_PASS'];
        $password = sha1($password . $db['SALT']);
        $query = "SELECT * FROM $loginTable WHERE `$usernameRow` = :username AND `$passwordRow` = :password LIMIT 1";
        $result = $this->query(
                $query, 
                array(
                    ':username' => $username, 
                    ':password' => $password
                ), TRUE);
        if ($result && is_array($result)) {
            $user = array_pop($result);

            //We update the Last_Login column
            //The reason we use 2 queries is because not all "user" tables may have both columns!
            $update = "UPDATE $loginTable SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
            $this->query($update, array(':id' => $user['id']));

            // We update the Attempts column
            $update = "UPDATE $loginTable SET attempts = 0 WHERE id = :id";
            $this->query($update, array(':id' => $user['id']));

            return $user;
        } else {
            return FALSE;
        }
    }
}
