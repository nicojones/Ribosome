<?php

/**
 * Class Model acts as a stepping stone (or "intermediate agent") between the user-defined code (<b>/src/models/</b>)
 * and the system-defined code (<b>ParentModel</b>) that helps to better structure the code. See &#64;example
 * @example
 * <code>
 * // To avoid:
 * FooController->queryProducts();
 * // and
 * BarController->queryProducts();
 * // to be defined twice (one in each controller) or once (in ParentController, bad code practices)
 * Controller->queryProducts();
 * // can be defined and thus accessed from both <b>Foo</b> and <b>Bar</b> Controllers.
 * </code>
 */
class Model extends \Core\ParentModel
{

    /**
     * @var Model The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns a Model instance, creating it if it did not exist.
     * @return Model
     */
    public static function singleton()
    {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }
}
