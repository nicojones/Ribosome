<?php

namespace Core;
use Core\Providers\Config;

/**
 * Class ParentModel connects to the database and provides useful functions (<b>query</b>, <b>queryIndexed</b>,
 * <b>updateRow</b>, <b>getRow</b>). See each one for more details.
 * @author Nico Kupfer nico.kupfer&#64;mamasu.es
 */
class ParentModel {

    /**
     * @var ParentModel The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var \PDO The database object.
     */
    protected $db;

    /**
     * @var Config The instance of the Config class.
     */
    protected $config;

    /**
     * @var string The project ID. Set from <b>/app/config/config.ini</b>.
     */
    protected $id = __ID__;

    /**
     * Constructor of the ParentModel. Tries a connection to the database and throws exception on error
     * @throws \PDOException When there is a PDOException (i.e. wrong credentials) throws an Exception that is
     * captured and throwed to AppKernel for further processing
     * @internal
     */
    function __construct() {
        $this->config = Config::singleton();
        try {
            $params = $this->config->get('Database');
            if ($params['DB_SUPPORT'] == '0') {
                return;
            }
            $this->db = !isset($GLOBALS['db'])
                   ? ($GLOBALS['db'] = new \PDO ('mysql:host='.$params['HOST'].';dbname='.$params['DB_NAME'].';charset=utf8', $params['USERNAME'], $params['PASSWORD']))
                   :  $GLOBALS['db'];
        } catch (\PDOException $e) {
            if (isDev()) {
                throw $e;
            } else {
                throw new \Exception($this->config->get('Exceptions', 'DB_CONNECTION_ERROR'));
            }
        } catch (\Exception $e) {
            throw new \Exception($this->config->get('Exceptions', 'DB_CONNECTION_ERROR'));
        }
    }


    /**
     * Returns the class instance, creating it if it did not exist.
     * @return ParentModel
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }

        return self::$instance;
    }

    /**
     * Runs a given $query string and returns the PDO object.
     * @param string $query
     * @param array $params = array()
     * @param bool $fetchAll = FALSE perform a fetchAll(PDO::FETCH_ASSOC) or simply return the PDO object
     * @return \PDO
     */
    public function query($query, $params = array(), $fetchAll = FALSE) {
        $res = $this->db->prepare($query);
        $res->execute($params);
        if ($fetchAll) {
            return $res->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return $res;
        }
    }

    /**
     * Runs a given $query string and returns the instance of the $className.
     * @param string $query
     * @param array $params
     * @param string $className
     * @return \PDO
     */
    public function queryClass($query, $params, $className) {
        if (!is_string($className)) {
            return NULL;
        }
        $res = $this->db->prepare($query);
        $res->execute($params);
        return $res->fetchAll(\PDO::FETCH_CLASS, $className);
    }
    
    /**
     * Runs a given $query string and returns the PDO object or the fetched results,
     * indexed by the first column.
     * @param string $query
     * @param array $params
     * @param bool $unique = TRUE. If set to FALSE, each key will contain an array of values.
     * @return \PDO
     */
    public function queryIndexed($query, $params = array(), $unique = TRUE) {
        $res = $this->db->prepare($query);
        $res->execute($params);
        $rows = $res->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_ASSOC);
        if ($unique) {
            return array_map('reset', $rows);
        } else {
            return $rows;
        }
    }
    
    /**
     * Gets a single result from a given table name and a given column name
     * @param string $tableName
     * @param string $columnValue
     * @param string $columnName = 'id'
     * @return array with the fetched row
     */
    public function getRow($tableName, $columnValue, $columnName = 'id') {
        $query = 'SELECT `' . $columnName . '` AS `index`, t.* FROM ' . 
                 $tableName . ' t WHERE t.`' . $columnName . '` = :columnValue LIMIT 1';
        $result = $this->query($query, array(':columnValue' => $columnValue), TRUE);
        return $result ? $result[0] : array();
        
    }
    
    /**
     * Updates a given row of a given table to a given value
     * @param string $tableName Name of the table to update
     * @param string $columnName Name of the column to update
     * @param int $id The ID to index results
     * @param mixed $value The value to set
     * @return int The number of affected rows (1 or 0)
     */
    public function updateRow($tableName, $columnName, $id, $value) {
        $query = 'UPDATE ' . $tableName . ' SET `' . $columnName . '` = :value WHERE id = :id LIMIT 1';
        $res = $this->query($query, array(
            ':id' => $id,
            ':value' => $value
        ));
        return $res->rowCount();
    }
}