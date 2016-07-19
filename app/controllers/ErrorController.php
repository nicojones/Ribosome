<?php

namespace Core;

/**
 * Class ErrorController contains both default pages for <b>403</b> and <b>404</b>.
 * @package Core
 */
class ErrorController extends ParentController {

    /**
     * @var ErrorController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var mixed A model class, to be specified by its child
     */
    protected $model;

   /**
    * Returns the class instance, creating it if it did not exist.
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
     * Outputs a 404 header and shows a 404 message
     */
    public function showNotFoundPage() {
        $this->header(404);
        die('HTTP/1.0 404 Not Found');
    }

    /**
     * Outputs a 403 header and shows a 403 message
     */
    public function showAccessDeniedPage() {
        $this->header(403);
        die('HTTP/1.0 403 Forbidden');
    }
}