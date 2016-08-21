<?php
    /*
     * In this file we add method shortcuts
     */

    /**
     * @param string $var Returns the value of $config[$var].
     * @aliasFor Config::singleton()->get($var)
     *
     * @return mixed|null
     */
    function config($var) {
        return \Core\Config::singleton()->get($var);
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

