<?php

namespace Vendor;
use Core\Session;

/**
 * Class VendorController is the parent class for all plugins (located in the plugin folder).<br/>
 * Please notice that some functions, when called from plugins, have different names (<b>view</b>, <b>get</b>, <b>asset</b>). See &#64;see for more details.
 * @see VendorController@vendor_show
 * @see VendorController@vendor_get
 * @see VendorController@vendor_asset
 * @note Respect the directives for creating new plugins. See the example ones for more info.
 * @package Vendor
 */
class VendorController extends \Core\ParentController
{
    /**
     * @var VendorController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var string The path where each plugin is stored. Set individually in each plugin (children).
     */
    public $pluginFolder;

    /**
     * @var array Array to store information about the view: scripts, styles, views, title...
     */
    protected $vars;

    /**
     * Returns the class Singleton
     *
     * @return VendorController
     */
    public static function singleton () {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     *
     */
    public function __construct() {
        parent::__construct();

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
    }

    /**
     * This is an implementation of ParentController@show() to allow for plugins.
     *
     * Shows a view embedded inside a specified template and die()
     * Remember that both $view and $template can be absolute or relative paths
     *
     * @param string $view The path to the file, being '/' the 'html' directory
     * @param string|bool $template The name of the template. If no template is specified, we use the default one
     *
     * @throws \Exception specifying the type of missing view.
     */
    public function vendor_show ($view, $template = FALSE) {
        $absoluteView = (
            $view[0] === '/' ?
                $view :
                (__ROOT__ . $this->config->get('Vendor', 'VENDOR_FOLDER') . '/' . $this->pluginFolder . '/views/html/' . $view . '.php')
        );

        if ($template) {
            $absoluteTemplate = (
                $template[0] === '/' ?
                    $template :
                    (__ROOT__ . $this->config->get('Vendor', 'VENDOR_FOLDER') . '/' . $this->pluginFolder . '/views/templates/' . $template . 'Template.php')
            );
        } else {
            $absoluteTemplate = 'default';
        }
        parent::show($absoluteView, $absoluteTemplate);
    }

    /**
     * This is an implementation of ParentController@get() to allow for plugins.
     *
     * Returns a view, optionally embedded inside a specified template.
     * Remember that both $view and $template can be absolute or relative paths
     *
     * @param string $view The path to the file, being '/' the 'html' directory
     * @param array|[] $vars The vars that you want to show in the $view
     * @param string|bool $template The name of the template. If no template is specified, we use the default one
     *
     * @return string The rendered view / view+template
     *
     * @throws \Exception specifying the type of missing view.
     */
    public function vendor_get ($view, $vars = [], $template = FALSE) {
        $absoluteView = (
            $view[0] === '/' ?
                $view :
                (__ROOT__ . $this->config->get('Vendor', 'VENDOR_FOLDER') . '/' . $this->pluginFolder . '/views/' . $view . '.php')
        );

        if ($template) {
            $absoluteTemplate = (
                $template[0] === '/' ?
                    $template :
                    (__ROOT__ . $this->config->get('Vendor', 'VENDOR_FOLDER') . '/' . $this->pluginFolder . '/views/templates/' . $template . 'Template.php')
            );
        } else {
            $absoluteTemplate = '';
        }
        $getView = parent::get($absoluteView, $vars, $absoluteTemplate);

        return $getView;
    }

    /**
     * Saves the vendor directives (Folders, Controllers, Models, Routing and Permissions) to a cache file.<br/>
     * Outputs a JSON encoded array.
     */
    public function vendorSaveCache() {
        // if is authenticated on bootload.php or if we're working in local:
        if (isLocalServer() || $this->getGet('auth', '') == '8d0a49297f36ed144194ce447db3b4f72399b913') {
            $saved = $this->config->loadVendors(TRUE);
            $this->json([
                'success' => (int)$saved['success'],
                'responseData' => [
                    'message' => empty($saved['reason']) ? 'Updated' : $saved['reason']]]);
        } else {
            $this->json(['success' => 0, 'message' => 'Not Authenticated']);
        }
    }

    /**
     * Adds a style snippet to the $this->vendor_show() renderer.
     *
     * @param string $path The path under which the asset is located
     * @param bool|TRUE $local whether it's a relative (local) or absolute URL
     * @param bool $dummy To prevent StrictStandards warning
     *
     * @return VendorController
     */
    public function addStyle ($path, $local = TRUE, $dummy = TRUE) {
        if (!$local) {
            // We add it like a normal stylesheet: non-local and non-minimizable
            parent::addStyle($path, FALSE, FALSE);
        } else {
            $this->vars['styleSnippet'] .= "\r\n\t\t" . '<link href="' . $this->vendor_asset('css/' . $path, TRUE) . '" rel="stylesheet" type="text/css"/>';
        }
        return $this;
    }

    /**
     * Adds a script snippet to the $this->vendor_show() renderer.
     *
     * @param string $path The path under which the asset is located
     * @param bool|TRUE $local whether it's a relative (local) or absolute URL
     * @param bool $dummy To prevent StrictStandards warning
     *
     * @return VendorController
     */
    public function addScript ($path, $local = TRUE, $dummy = TRUE) {
        if (!$local) {
            // We add it like a normal script: non-local and non-minimizable
            parent::addScript($path, FALSE, FALSE);
        } else {
            $this->vars['scriptSnippet'] .= "\r\n\t\t" . '<script src="' . $this->vendor_asset('js/' . $path, TRUE) . '" type="text/javascript"></script>';
        }
        return $this;
    }

    /**
     * Echoes or returns the absolute path to the asset
     *
     * @param string $path The path under which the view is located
     * @param bool|FALSE $return Whether to return or echo
     *
     * @return string|VendorController The absolute path (or $this if 'echo')
     */
    public function vendor_asset($path, $return = FALSE) {
        $assetPath = $this->path . 'vendor_' . $this->pluginFolder . '/' . $path;
        if ($return) {
            return $assetPath;
        } else {
            echo $assetPath;
            return $this;
        }
    }

    /**
     * Adds the $_header variable to the view
     * @param array $vars|[] The vars to pass to the header
     * @param string|bool $headerLocation an (optional) alternative route for the header
     * @return VendorController
     */
    public function addHeader($vars = [], $headerLocation = FALSE) {
        $header = $headerLocation ?
            $this->get($headerLocation, $vars) :
            $this->vendor_get('html/blocks/header', $vars);
        $this->add('_header', $header);
        return $this;
    }

    /**
     * Adds the $_footer variable to the view
     * @param array $vars|[] The vars to pass to the footer
     * @param string|bool $footerLocation an (optional) alternative route for the footer
     * @return VendorController
     */
    public function addFooter($vars = [], $footerLocation = FALSE) {
        $footer = $footerLocation ?
            $this->get($footerLocation, $vars) :
            $this->vendor_get('html/blocks/footer', $vars);
        $this->add('_footer', $footer);
        return $this;
    }
}