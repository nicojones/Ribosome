<?php

    namespace Core;
    use Core\Providers\Service;

    /**
     * Loads and sets the language <b>*.ini</b> files.
     * @package Language
     */
    class Language
    {

        /**
         * @var mixed $instance A class instance
         * @internal
         */
        private static $instance;

        /**
         * @var string The "accept_lang" property of the client's browser
         */
        protected $acceptLang;
        /**
         * @var bool (and equals !$acceptLang) whether the user is a bot or not.
         * @note All browsers send the accept_lang parameter, so a lack of such property will mean a lack of browser.
         */
        protected $isBot;

        /**
         * @var array The translations loaded for a given language
         */
        protected $translations = array();

        /**
         * @var array The configuration setting for languages
         */
        protected $languages = [
            'default' => 'en',
            'languages' => ['es', 'en']
        ];

        /**
         * @var Service The service model
         */
        protected $service;

        /**
         *
         * /**
         * Returns the Language instance, creating it if it did not exist.
         * @return Language
         */
        public static function singleton()
        {
            if (!self::$instance) {
                $c = __CLASS__;
                self::$instance = new $c();
            }
            return self::$instance;
        }

        /**
         * Populates the default properties of Language class
         */
        public function __construct()
        {
            $this->acceptLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : false;
            $this->isBot = !$this->acceptLang;
            $this->service = Service::singleton();
        }

        public function getModel() {

        }

        /**
         * Sets the language for the user, by populating <code>$this->translations</code> for later use.
         * @see function t()
         */
        public function set() {
            /* If it's a bot, we use the default language */
            if ($this->isBot) {
                $this->get_user_language(false);

            /* If the language is already set in $_SESSION but we're NOT setting a new one through $_GET: */
            } elseif (isset($_SESSION['lang']) && !isset($_GET['lang'])) { /* Only if we're not asking for a different language! */
                $this->translations = Service::getLanguage($_SESSION['lang']);

            /* Else: we set the language based on $_GET (if set) or based on the browser language */
            } else {
                $preferredLanguage = isset($_GET['lang']) ? $_GET['lang'] : $this->acceptLang;
                $lang = $this->get_user_language($preferredLanguage);

                $_SESSION['lang'] = $lang;
                $uri = explode('?', $_SERVER['REQUEST_URI']);

                if (isset($_GET['noredirect'])) {
                    $uri[0] = substr($uri[0],0,strlen($uri[0])-3);
                }

                $this->translations = Service::getLanguage($lang);

                header ("Location: " . $uri[0]);
            }
        }

        /**
         * Searches and retrieves the translation associated to the $key. Returns $key if it doesn't exist
         * (useful for the default language)
         *
         * @param string $key The translation key
         *
         * @return string|bool
         */
        public function getTranslation($key) {
            return array_key_exists($key, $this->translations) ? $this->translations[$key] : $key;
        }

        /**
         * @param string|bool $preferredLanguage The language we will show to the user. Coincides with their browser
         * language if it's translated
         *
         * @return string The user language (if it's translated) or the default (if it's not)
         */
        protected function get_user_language($preferredLanguage = false) {
            if (in_array($preferredLanguage, $this->languages['languages'])) {
                return $preferredLanguage;
            } else {
                return $this->languages['default'];
            }
        }

    }