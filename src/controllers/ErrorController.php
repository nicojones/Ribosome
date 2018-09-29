<?php

/**
 * Used as a user-defined controller for errors and Error pages.
 */
class ErrorController extends \Core\ErrorController {

    /**
     * @var ErrorController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var mixed The model instance associated to ErrorController
     */
    protected $model;

    /**
     * Returns a ErrorController instance, creating it if it did not exist.
     * @return ErrorController
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * This method is overriding \Core\ErrorController:showNotFoundPage
     */
    public function showNotFoundPage() {
        $this->header(404);

        if ($asset = $this->getGet('asset', FALSE)) {
            if (in_array($asset, ['css', 'js', 'fonts'])) {
                die("/* the asset " . $_SERVER['REQUEST_URI'] . " could not be found... */");
            } else {
                die(); // it's an asset, there should be no output!
            }
        } else {
            $this->show('error/404_not_found');
        }
    }
}