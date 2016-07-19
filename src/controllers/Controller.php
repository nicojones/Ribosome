<?php

/**
 * Class Controller acts as a stepping stone (or "intermediate agent") between the user-defined code (<b>/src/controllers/</b>)
 * and the system-defined code (<b>ParentController</b>) that helps to better structure the code. See &#64;example
 * @example
 * <code>
 * // To avoid:
 * FooController->showProducts();
 * // and
 * BarController->showProducts();
 * // to be defined twice (one in each controller) or once (in ParentController, bad code practices)
 * Controller->showProducts();
 * // can be defined and thus accessed from both <b>Foo</b> and <b>Bar</b> Controllers.
 * </code>
 */
class Controller extends \Core\ParentController
{

    /**
     * @var Controller The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * @var Model The instance of Model.
     */
    protected $model;

    /**
     * Returns a Controller instance, creating it if it did not exist.
     * @return Controller
     */
    public static function singleton()
    {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function __construct() {
        parent::__construct();
        $this->model = Model::singleton();
    }
}
