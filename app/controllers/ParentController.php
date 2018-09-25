<?php

namespace Core;
use Core\Helpers\Hooks;
use Core\Providers\Config;
use Core\Exceptions\Exception;

/**
 * This is the parent class for all controllers in <b>/src/controllers</b>, and indirectly for all controllers
 * that extend <b>VendorController</b> (which, in turn, extend this).<br/>
 * ParentController is the class that manages assets, paths, $_GET and $_POST, views, etc.
 *
 * @hooks
 * <code>
 * ('include_show_view', ['vars' => $this->vars]) // Called just before including the view file {@see \Core\ParentController::show}
 * ('include_get_view', ['vars' => $_vars]) // Called just before including a view file  {@see \Core\ParentController::get}
 * ('extra_params_path', ['route' => $route]) // If extra parameters are required when creating a url {@see \Core\ParentController::path}
 * </code>
 *
 * @author Nico Kupfer nico&#64;kupfer.es
 */
class ParentController {

    /**
     * @var ParentController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var string The path where the framework is located: &lt;framework_url&gt;/<b>&lt;path&gt;</b>/app.php.
     */
    protected $path;

    /**
     * @var Config The instance of the Config class.
     */
    protected $config;

    /**
     * @var array The required variables to show a view.
     * @see ParentController::show
     */
    protected $vars;

    /**
     * @var bool If the request is an AJAX call. Set automatically by reading <b>$_SERVER['HTTP_X_REQUESTED_WITH']</b>
     */
    protected $ajax;

    /**
     * @var ParentModel The instance of ParentModel.
     */
    protected $model;

    /**
     * @var string Default path for files. For example, {view} = "home/index".
     */
    protected $viewPath;

    /**
     * @var string Default path for templates. For example, {template} = "default" or "email".
     */
    protected $templatePath;

    /**
     * @var \Core\Hooks The instance of the Hooks class.
     */
    protected $hooks;

    /**
     * Every Controller that extends ParentController must explicitly
     */
    public function __construct() {
        $this->path = __PATH__ . '/';
        $this->config = Config::singleton();
        $this->model = ParentModel::singleton();
        $this->vars = [
            'view' => ['_header' => '', '_footer' => ''],
            'JS' => '',
            'script' => [],
            'scriptSnippet' => '',
            'style' => [],
            'styleSnippet' => '',
            'favicon' => '',
            'title' => ''
        ];
        $this->ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        $this->viewPath = __ROOT__ . '/src/resources/views/{view}.php';
        $this->templatePath = __ROOT__ . '/src/resources/views/templates/{template}Template.php';
        $this->hooks = Hooks::getInstance();//$GLOBALS['hooks'];
    }

    /**
     * Returns a ParentController instance, creating it if it did not exist.
     * @return ParentController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;

        }
        return self::$instance;
    }


    /**
     * Returns the instance of the model for this controller
     * @return ParentModel
     */
    public function getModel() {
        return $this->model;
    }

   /**
    * Filters an input (GET) variable and returns it. If key doesn't exist or the value equals NULL, returns $fallback
    *
    * @var string $key The key
    * @var mixed $fallback if not null, would be taken as the default value. Otw the parameter is required.
    * @return mixed $_GET[$key] or $fallback
    */
    function getGet($key, $fallback = NULL) {         
        $value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($value) && isset($_GET[$key])) {
            $value = $_GET[$key];
        }
        if ($value !== NULL) {
            return $value;
        } else {
            return $fallback;
        }
    }

   /**
    * Filters an input (POST) variable and returns it. If key doesn't exist or the value equals NULL, returns $fallback
    *
    * @var string $key The key
    * @var mixed $fallback if not null, would be taken as the default value. Otw the parameter is required.
    * @return mixed $_POST[$key] or $fallback
    */
    function getPost($key, $fallback = NULL) {         
        $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($value) && isset($_POST[$key])) {
            $value = $_POST[$key];
        }
        if ($value !== NULL) {
            return $value;
        } else {
            return $fallback;
        }
    }

    /**
     * Shows a view inside a specified template
     * Remember that both $view and $template can be absolute or relative paths
     *
     * @param string $view The path to the file, being '/' the 'views' directory
     * @param string $template The name of the template
     *
     * @throws \Exception specifying the type of missing view.
     * @throws \Exception specifying the type of missing view.
     */
    public function show($view, $template = 'default') {
        ob_start();
        extract($this->vars['view']);
        if (file_exists($file = ($view[0] === '/' ? $view : str_replace('{view}', $view, $this->viewPath)))) {
            try {
                /* Useful if you need to include extra things to a view */
                $this->hooks->do_action('include_show_view', ['vars' => $this->vars]);
                /* We include the view file */
                include $file;
            } catch (\Exception $e) {
                ob_end_clean();
                Exception::singleton()->showException($e);
            }
        } else {
            throw new \Exception(str_replace('[[FILE]]', $file, $this->config->get('Exceptions', 'VIEW_NOT_FOUND')));
        }
        $_body = ob_get_clean();

        foreach ($this->vars['script'] as $s) {
            $this->addScript($this->asset('js/' . $s, TRUE), FALSE);
        }
        foreach ($this->vars['style'] as $s) {
            $this->addStyle($this->asset('css/' . $s, TRUE), FALSE);
        }

        $_js      = $this->vars['JS'];
        $_title   = $this->vars['title'];
        $_style   = $this->vars['styleSnippet'];
        $_script  = $this->vars['scriptSnippet'];
        $_favicon = $this->vars['favicon'];

        if (file_exists($templateFile = ($template[0] === '/' ? $template : str_replace('{template}', $template, $this->templatePath)))) {
            try {
                /* We include the template file */
                include $templateFile;
            } catch (\Exception $e) {
                ob_end_clean();
                Exception::singleton()->showException($e);
            }
        } else {
            throw new \Exception(str_replace('[[FILE]]', $template, $this->config->get('Exceptions', 'TEMPLATE_NOT_FOUND')));
        }
    }

    /**
     * Renders a view and returns it
     * @param string $view The path to the file, being '/' the 'views' directory
     * @param array $_vars The $vars to include in the view
     * @param string $template The template name
     *
     * @return string The requested view
     *
     * @throws \Exception specifying the type of missing view.
     */
    public function get($view, $_vars = array(), $template = '') {
        ob_start();
        extract($_vars);
        if (file_exists($file = ($view[0] === '/' ? $view : str_replace('{view}', $view, $this->viewPath)))) {
            try {
                /* Useful if you need to include extra things to a view */
                $this->hooks->do_action('include_get_view', ['vars' => $_vars]);

                /* We include the view file */
                include $file;
            } catch (\Exception $e) {
                ob_end_clean();
                Exception::singleton()->showException($e);
            }
        } else {
            throw new \Exception(str_replace('[[FILE]]', $file, $this->config->get('Exceptions', 'VIEW_NOT_FOUND')));
        }
        $_body = ob_get_clean();
        if ($template) {
            ob_start();
            if (file_exists($templateFile = ($template[0] === '/' ? $template : str_replace('{template}', $template, $this->templatePath)))) {
                try {
                    /* We include the template file */
                    include $templateFile;
                } catch (\Exception $e) {
                    ob_end_clean();
                    Exception::singleton()->showException($e);
                }
            } else {
                throw new \Exception(str_replace('[[FILE]]', $template, $this->config->get('Exceptions', 'TEMPLATE_NOT_FOUND')));
            }
            $_body = ob_get_clean();
        }
        return $_body;
    }

    /**
     * Returns the web path for a given URL
     * @param string $path
     * @param bool $return
     * @return string $webPath
     */
    public function asset($path = '', $return = FALSE) {
        $fullPath = $this->path . $path;
        if ($return) {
            return $fullPath;
        } else {
            echo $fullPath;
        }
    }
    
    /**
     * Returns the web path for a given Route name
     * @param string $path The path identifier ('Home', 'Login', ...)
     * @param array $params The parameters of the URL (':id', ':name', ...)
     * @param bool $return = FALSE. Whether to return or echo
     * @return mixed If $return = FALSE, the path. NULL otherwise
     * @throws \Exception If $path does not exist in the routing.ini
     */
    public function path($path = '', $params = array(), $return = FALSE) {
        $route = $this->config->get('Routing', $path) ?:
                 $this->config->get('Routing', 'Vendor\\' . $path);
        if (!$route) {
            ob_end_flush();
            throw new \Exception(str_replace('[[ROUTE]]', $path, $this->config->get('Exceptions', 'ROUTE_NOT_FOUND')));
        }

        $arrayKeys = array_keys($params);
        $arrayValues = array_values($params);
        $uri = str_replace($arrayKeys, $arrayValues, $route['path'], $count);
        if ($count < $arrayKeys) {

            // this means there are params not INSIDE the URL. So we need to append them...
            $extraParams = [];
            foreach ($arrayKeys as $key => $ak) {
                if (strpos($route['path'], $ak) === FALSE) {
                    $extraParams[substr($ak, 1)] = $arrayValues[$key];
                }
            }
            if (count($extraParams)) {
                $uri .= '?' . http_build_query($extraParams);
            }
        }

        if ($return) {
            return $this->path . $uri;
        } else {
            echo $this->path . $uri;
        }
    }
    
    /**
     * Alias for ::path with $return = TRUE. Use it for controllers
     * @param string $path The path identifier ('Home', 'Login', ...)
     * @param array $params The parameters of the URL (':id', ':name', ...)
     * @return string The formatted URL
     * @throws \Exception if $path doesn't correspond to any routing.ini key.
     */
    public function url($path, $params = array()) {
        return $this->path($path, $params, TRUE);
    }

    /**
     * Performs a header redirection and a die();
     * @param string $path
     *
     * @return mixed A JSON string with redirect instructions (if it's an ajax call) or a Location header.
     */
    public function redirect($path = '') {
        if ($this->ajax) {
            return $this->json(['redirect' => $path]);
        } else {
            header ('Location: ' . $path);
            die();
        }
    }

    /**
     * Adds a var to the $this->show() renderer.
     * @param string $varName
     * @param mixed $value
     * @return ParentController
     */
    public function add($varName, $value) {
        $this->vars['view'][$varName] = $value;
        return $this;
    }

    /**
     * Adds a JS variable to the $this->show() renderer.
     * @param string $var
     * @param mixed $value
     * @param bool $encoded
     * @return ParentController
     */
    public function addJSVar($var, $value, $encoded = TRUE) {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                $this->addJSVar($k, $v);
            }
            return $this;
        }
        $js = $this->vars['JS'];
        $js .= "\n" . 'var ' . $var . ' = ' . ($encoded ? json_encode($value) : $value) . ';';
        $this->vars['JS'] = $js;
        return $this;
    }

    /**
     * Adds a script snippet to the $this->show() renderer.
     * @param string $path The path to the asset (local or absolute)
     * @param bool|TRUE $local whether it's a relative (local) or absolute URL
     * @return ParentController
     */
    public function addStyle($path, $local = TRUE) {
        if ($local) {
            $this->vars['style'][] = $path;
        } else {
            $this->vars['styleSnippet'] .= "\r\n\t\t" . '<link href="' . ($local ? $this->asset('css/' . $path, TRUE) : $path) . '" rel="stylesheet" type="text/css"/>';
        }
        return $this;
    }

    /**
     * Adds a style snippet to the $this->show() renderer.
     * @param string $path The path to the asset (local or absolute)
     * @param bool|TRUE $local whether it's a relative (local) or absolute URL
     * @return ParentController
     */
    public function addScript($path, $local = true) {
        if ($local) {
            $this->vars['script'][] = $path;
        } else {
            $this->vars['scriptSnippet'] .= "\r\n\t\t" . '<script src="' . ($local ? $this->asset('js/' . $path, TRUE) : $path). '" type="text/javascript"></script>';
        }
        return $this;
    }


    /**
     * Adds the $_header variable to the view
     * @param array $vars|[] The vars to pass to the header
     * @param string|bool $headerLocation an (optional) alternative route for the header
     * @return ParentController
     * @throws \Exception
     */
    public function addHeader($vars = [], $headerLocation = FALSE) {
        $header = $headerLocation ?
            $this->get($headerLocation, $vars) :
            $this->get('blocks/header', $vars);
        $this->add('_header', $header);
        return $this;
    }

    /**
     * Adds the $_footer variable to the view
     * @param array $vars|[] The vars to pass to the footer
     * @param string|bool $footerLocation an (optional) alternative route for the footer
     *
     * @return ParentController
     * @throws \Exception
     */
    public function addFooter($vars = [], $footerLocation = FALSE) {
        $footer = $footerLocation ?
            $this->get($footerLocation, $vars) :
            $this->get('blocks/footer', $vars);
        $this->add('_footer', $footer);
        return $this;
    }
    
    /**
     * Sets a title for the website (different from the default one)
     * @param string $title
     *
     * @return ParentController
     */
    public function setTitle($title) {
        $this->vars['title'] = $title;
        return $this;
    }
    
    /**
     * Sets a favicon for the website (different from the default one)
     * @param string $favicon Route to the favicon asset
     *
     * @return ParentController
     */
    public function setFavicon($favicon) {
        $this->vars['favicon'] = $favicon;
        return $this;
    }

    /**
     * Returns $obj in a JSON format
     * @param mixed $obj
     * @param int $flags Flags to parse to {@see json_encode}
     * @usage
     * This function transforms a single-level array into a 2+ level array for the response
     * <code>
     * ['success' => true, 'message' => 'Saved!', 'foo' => 'bar']
     * ['message' => 'Saved!', 'foo' => 'bar']
     * </code>
     * Get transformed into
     * <code>
     * ['success' => 1 // true is always default, and the value gets cast to integer (int)
     *  'responseData' => [
     *      'message' => 'Saved!', // by default is empty
     *      'foo' => 'bar' // custom
     * ]]
     * </code>
     * You are free to add as many variables as you like. They'll be added under 'responseData' key.
     *
     * @return string JSON_encoded $obj
     */
    public function json($obj, $flags = 0) {
        $defaults = [
            'success' => 1,
            'message' => ''
        ];

        $response = $obj;
        foreach ($defaults as $k => $d) {
            if (!isset($response[$k])) {
                $response[$k] = $d;
            } else {
                unset($obj[$k]);
            }
        }

        $responseObj = [
            'success' => (int)$response['success'],
            'responseData' => [
                'message' => $response['message'],
            ]
        ];
        foreach ($obj as $k => $o) {
            $responseObj['responseData'][$k] = $o;
        }

        die(json_encode($responseObj, $flags));
    }

    /**
     * Gets the cache content for a given path
     * @param string $path
     * @param array $vars Parameters to which render the file
     *
     * @return string rendered file
     * @throws \Exception When the $path does not correspond (remember, there's a / -> _ conversion) to a chached file
     */
    public function cache($path, $vars = []) {
        $file = __ROOT__ . '/app/cache/' . str_replace('/', '_', $path);
        if (file_exists($file)) {
            ob_start();
            /* @TODO improve the extract() by adding a prefix */
            extract($vars);
            include $file;
            $cache = ob_get_clean();
            return $cache;
        } else {
            throw new \Exception(str_replace('[[FILE]]', $file, $this->config->get('Exceptions', 'VIEW_NOT_FOUND')));
        }
    }
    
    /**
     * Outputs the correspondent header for the given HTTP code, on the form header('HTTP/1.0 404 Not Found');
     * See description of the status codes in the provided url
     * @url http://www.w3.org/Protocols/HTTP/1.0/spec.html#Status-Codes
     * @param int $code = 200 The status code
     * @return ParentController
     */
    public function header($code = 200) {
        $codes = array(
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '204' => 'No Content',
            '301' => 'Moved Permanently',
            '302' => 'Moved Temporarily',
            '304' => 'Not Modified',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable');
        if (!empty($codes[$code])) {
            header('HTTP/1.0 ' . $code . ' ' . $codes[$code]);
        }
        return $this;
    }
}