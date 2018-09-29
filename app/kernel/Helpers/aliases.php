<?php
    /*
     * In this file we add method shortcuts
     */

    /**
     * @param string $var Returns the value of $config[$var].
     * @param string $subVar (optional) Returns the value of $config[$var][$subVar].
     * @aliasFor Config::singleton()->get($var)
     *
     * @return mixed|null
     */
    function config($var, $subVar = null) {
        return \Core\Config::singleton()->get($var, $subVar);
    }

    /**
     * @param string $urlKey The routing [key] (as seen in routing.ini) for the desired URL
     * @param array $params Extra parameters to the url
     *
     * @throws Exception
     */
    function path($urlKey, $params = []) {
        echo \Core\ParentController::singleton()->url($urlKey, $params);
    }

    /**
     * @param string $src path to the asset (image, file...). No leading slash,
     * @param bool|false $return return or echo
     * @return string|null
     */
    function asset($src, $return = false) {
        return \Core\ParentController::singleton()->asset($src, $return);
    }

    /**
     * @param string $view The location of the $template within the /src/view folder
     * @param array $vars The vars to add to the view
     * @param string $template An optional template if we don't want to use defaultTemplate.php
     *
     * @return string The rendered $template with $vars
     * @throws Exception If a render problem occurs
     */
    function view($view, $vars = [], $template = '') {
        echo \Core\ParentController::singleton()->get(str_replace('.', '/', $view), $vars, $template);
    }

    function t($translationKey) {
        $l = \Core\Language::singleton();
        $word = $l->getTranslation($translationKey);
        return $word ?: $l->getDefaultTranslation($translationKey);
    }

    /**
     * @return string The environment (global var <b>__ENVIRONMENT__</b>) you're working in.
     */
    function env() {
        return defined('__ENVIRONMENT__') ? __ENVIRONMENT__ : FALSE;
    }

    /**
     * @return bool Whether the environment (global var <b>__ENVIRONMENT__</b>) is production (<b>= "prod"</b>)
     */
    function isProd() {
        return defined('__ENVIRONMENT__') &&  __ENVIRONMENT__ == 'prod';
    }

    /**
     * @return bool Whether the environment (global var <b>__ENVIRONMENT__</b>) is developement (<b>= "dev"</b>)
     */
    function isDev() {
        return defined('__ENVIRONMENT__') && __ENVIRONMENT__ == 'dev';
    }

    /**
     * @return bool Whether the user is authenticated (true) or not (false)
     */
    function isAuth() {
        return \Core\Providers\Session::getUser('role') >= __ROLE_ADMIN__;
    }

    /**
     * @return bool Whether the request was made through an AJAX call
     */
    function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Returns whether the PHP server is localhost or remote. True if the host matches <b>*.local</b> or <b>localhost</b>.
     * From the Symfony framework.
     * @return bool TRUE if the $_SERVER is a local machine; FALSE if it's a remote machine
     */
    function isLocalServer() {
        $remoteServer = (
            isset($_SERVER['HTTP_CLIENT_IP'])
            || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
                || php_sapi_name() === 'cli-server')
        );
        return !$remoteServer;
    }


