<?php

    namespace Core;

    /**
     * Class Service is our core backbone to retrieve information from the server before the instance is finished.
     * @package Core
     */
    class Service extends ParentModel
    {

        /**
         * @var Service The class instance.
         * @internal
         */
        protected static $instance;

        /**
         * @var ParentModel The instance of the ParentModel
         */
        protected $model;

        /**
         * Returns a Service instance, creating it if it did not exist.
         * @return Service
         */
        public static function singleton() {
            if (!self::$instance) {
                $v = __CLASS__;
                self::$instance = new $v;
            }
            return self::$instance;
        }

        public function __construct() {
        }

        /**
         * Returns an array with the parsed translations from the <code>{lang}.ini</code> file.
         * @param string $lang The language key
         *
         * @return array The translations
         */
        public static function getLanguage($lang) {
            // Server query to retrieve the language array
            $translations = [];
            // we get the vector $translations from the language file
            include (__ROOT__ . '/src/resources/languages/' . $lang . '.php');
            return $translations;

            // Dummy: This was done by \Core\Language but we've moved it here.
        }
    }