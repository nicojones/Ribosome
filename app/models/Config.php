<?php

namespace Core;

/**
 * Loads and sets <b>*.ini</b> files, which are the configuration files for the Framework.
 * @package Core
 */
class Config
{

    /**
     * @var mixed $instance A class instance
     * @internal
     */
    private static $instance;

    /**
     * @var array The key under which to store all quick-access information.
     */
    private $vars;

    /**
     * @var array An auxiliary key to store ("remember") the already-loaded <b>*.ini</b> files.
     */
    private $ini;

    /**
     * @var array An auxiliary key to store ("remember") all details that must be cached. After the execution, <b>$cacheIni</b>
     * contains all such information.
     */
    private $cacheIni;

    /**
     * @var string The <b>/app/config/config.ini</b> key that contains the <b>Global</b> variables to set.
     */
    private $globalsKey = 'Globals';

    /**
     * @var string The <b>/app/config/config.ini</b> key that contains the <b>ini_set()</b> directives.
     */
    private $phpiniKey = 'PHP_ini';

    private function __construct()
    {
        $this->vars = array();
    }

    /**
     * Returns the Config instance, creating it if it did not exist.
     * @return Config
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
     * Sets a $value to a given $key
     * @param string $key
     * @param mixed $value
     *
     * @return Config
     */
    public function set($key, $value = NULL)
    {
        if (array_key_exists($key, $this->vars)) {
            $this->vars[$key] = array_merge($this->vars[$key], $value);
        } else {
            $this->vars[$key] = $value;
        }
        return self::$instance;
    }

    /**
     * Gets the $value of a given $key
     * @param string $key
     * @param string|bool $subKey
     * @return mixed|null $value associated to the $key or NULL
     */
    public function get($key, $subKey = FALSE)
    {
        if (isset($this->vars[$key])) {
            if ($subKey) {
                if (isset($this->vars[$key][$subKey])) {
                    return $this->vars[$key][$subKey];
                } else {
                    return NULL;
                }
            }
            return $this->vars[$key];
        } else {
            return NULL;
        }
    }

    /**
     * Loads an ini file
     * @param string|bool $path The file (path+file) to load
     * @param string|bool $absolute If not specified, the file will be searched inside /config folder
     * @param string|bool $iniKey = FALSE If specified, all the ini file will be saved under that key
     * @param string|bool $namespace = FALSE If specified, the globals will be defined inside the namespace
     *
     * @return Config
     */
    public function load($path = FALSE, $absolute = FALSE, $iniKey = FALSE, $namespace = FALSE)
    {
        $vars = $this->getIni($path, $absolute);

        if (!$iniKey) {
            foreach ($vars as $key => $value) {
                if ($key === $this->globalsKey) {
                    $this->setGlobals($value, (string)$namespace);
                } elseif ($key === $this->phpiniKey) {
                    $this->setPHPIniParams($path);
                } else {
                    $this->set($key, $value, TRUE);
                }
            }
        } else {
            $this->set($iniKey, $vars);
        }
        return self::$instance;
    }

    /**
     * Loads global vars from an .ini file
     * @param array $globals The globals to load
     * @param string| $namespace If specified, the globals will be defined inside the namespace
     *
     * @return Config
     */
    public function setGlobals($globals, $namespace = "")
    {
        foreach ($globals as $key => $value) {
            if (!defined($namespace . $key)) {
                DEFINE($namespace . $key, $value);
            }
            //if (!defined('__' . $key . '__')) {
            //    DEFINE('__' . $key . '__', $value);
            //}
        }
        return self::$instance;
    }

    /**
     * Loads the ini_set parameters from an .ini file
     * @param string|bool $path
     * @return Config
     */
    public function setPHPIniParams($path = FALSE)
    {
        $vars = $this->getIni($path);
        foreach ($vars['PHP_ini'] as $key => $value) {
            ini_set($key, $value);
        }
        return self::$instance;
    }

    /**
     * Loads the extra permission.ini and routing.ini from /src/vendor folders
     *
     * @param bool|FALSE $saveToCache Save in /app/cache a config which loads all vendor *.ini and includes.
     *
     * @throws \Exception if directory doesn't exist
     * @return Config
     */
    public function loadVendors ($saveToCache = FALSE)
    {
        if (isProd() &&
            (!$saveToCache)) {
            $this->loadCacheIni();
            return self::$instance;
        }

        if ($this->get('Vendor', 'VENDOR_ENABLED') == '0') {
            return self::$instance;
        }
        // else: we load the Vendor directory from the config file
        $vendorFolderConfig = __ROOT__ . $this->get('Vendor', 'VENDOR_FOLDER');

        if (!file_exists($vendorFolderConfig)) {
            throw new \Exception($this->get('Exceptions', str_replace('[[DIR]]', $vendorFolderConfig, 'VENDOR_DIR_NOT_FOUND')));
        }
        $files = scandir($vendorFolderConfig);
        foreach ($files as $f) {
            if ($f[0] == '.') {
                continue;
            }
            $vendorFolder = $vendorFolderConfig . '/' . $f;
            $this->load($vendorFolder . '/config/config.ini', TRUE, FALSE, "Vendor\\$f\\")
                 ->load($vendorFolder . '/config/permissions.ini', TRUE, 'Permissions')
                 ->load($vendorFolder . '/config/routing.ini', TRUE, 'Routing')
                 ->includeVendorLogic($vendorFolder . '/controllers')
                 ->includeVendorLogic($vendorFolder . '/models');

            $this->cacheIni['Config'][$f] = $vendorFolder . '/config/config.ini';
            $this->cacheIni['Permissions'][$f] = $vendorFolder . '/config/permissions.ini';
            $this->cacheIni['Routing'][$f] = $vendorFolder . '/config/routing.ini';
        }

        // we leave it for now. We want each "dev" execution to save the vendor_ini configuration.
        $saved = $this->saveVendorCache();

        if ($saveToCache) {
            return $saved;
        }

        return self::$instance;
    }

    /**
     * Scans the given $folder and "includes" its content.
     * @param string $folder The folder where to scan for Controllers or Models
     *
     * @return Config
     */
    private function includeVendorLogic($folder)
    {
        $files = scandir($folder);
        foreach ($files as $f) {
            if ($f[0] == '.') {
                continue;
            }
            // We include each Logic file (*Controller.php, *Model.php) from the vendor folder
            include_once $folder . '/' . $f;
            $this->cacheIni['Includes'][] = $folder . '/' . $f;
        }
        return self::$instance;
    }

    /**
     * Reads an .ini file
     * @param string $path
     * @param string|bool $absolute If not specified, the file will be searched inside /config folder
     * @return array INI file, array-like
     */
    private function getIni ($path, $absolute = FALSE)
    {
        if (!$path) {
            return array();
        }
        if (!$absolute) {
            $path = __ROOT__ . '/app/config/' . $path;
        }

        // there's no file, so we return "[]"
        if (!file_exists($path)) {
            return [];
        }
        // If the file exists, we parse_ini_file it and return its contents (as array[])
        if (empty($this->ini[$path])) {
            $this->ini[$path] = parse_ini_file($path, TRUE);
        }
        return $this->ini[$path];
    }

    /**
     * Saves the private global var $this->cacheIni to the <b>vendor_ini.ini</b> file
     *
     * @return array With information about success or failure
     */
    public function saveVendorCache ()
    {
        $saved = save_ini_file($this->cacheIni, TRUE, __ROOT__ . '/app/cache/vendor_ini.ini');
        if ($saved['success'] == 1) {
            $this->cacheIni = [];
        }
        return $saved;
    }

    /**
     * Loads the vendor_ini.ini cache file
     *
     * @return Config
     */
    private function loadCacheIni ()
    {
        $vendorIniFile = __ROOT__ . '/app/cache/vendor_ini.ini';
        $vendorIniExists = file_exists($vendorIniFile);
        if (!$vendorIniExists) {
            die('You must run your web app at least once with <b>__ENVIRONMENT__</b> = "<b>dev</b>"
                 (currently set to "<b>' . __ENVIRONMENT__ . '</b>")');
        }
        $vendorIni = $this->getIni($vendorIniFile, TRUE);

        if (empty($vendorIni)) {
            die('There was a problem when reading the file: it seems to be <b>empty</b>.<br/>
                 You must manually set <b>__ENVIRONMENT__</b> = "<b>dev</b>" and refresh the page');
        }

        foreach ($vendorIni['Includes'] as $i) {
            include_once $i;
        }
        foreach ($vendorIni['Config'] as $namespace => $r) {
            $this->load($r, TRUE, FALSE, "Vendor\\$namespace\\");
        }
        foreach ($vendorIni['Routing'] as $r) {
            $this->load($r, TRUE, 'Routing');
        }
        foreach ($vendorIni['Permissions'] as $p) {
            $this->load($p, TRUE, 'Permissions');
        }
        return self::$instance;
    }

    /**
     * Returns the private $vars
     * @return array $vars
     */
    public function getLoadedConfig () {
        return $this->vars;
    }
}