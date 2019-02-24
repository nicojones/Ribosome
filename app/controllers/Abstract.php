<?php

    namespace Core;


    /**
     * This class defines the basic structure of any Controller
     *
     * Class AbstractClass
     * @package Core
     */
    abstract class AbstractClass {

        /**
         * @var CLASS Controller The class instance.
         * @internal
         */
        protected static $instance;

        /**
         * @var CLASS Model The instance of the model
         */
        protected $model;


        /**
         * Returns a LoginController instance, creating it if it did not exist.
         * @return bool CLASS Controller
         */
        final public static function singleton()
        {
            if (static::$instance === null) {
                $v = __CLASS__;

                static::$instance = new $v();// self();
            }

            return static::$instance;
        }

        /**
         * Returns the instance of the model for this controller
         * @return bool CLASS
         */
        final public function getModel() {
            return $this->model;
        }
    }