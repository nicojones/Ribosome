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
        echo \Core\ParentController::singleton()->get('html/' . str_replace('.', '/', $view), $vars, $template);
    }

    function t($translationKey) {
        $l = \Core\Language::singleton();
        $word = $l->getTranslation($translationKey);
        return $word ?: $l->getDefaultTranslation($translationKey);
    }

