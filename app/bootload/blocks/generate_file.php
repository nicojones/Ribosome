<?php
    if (!empty($controller)) {
        $code = "<?php

";
        if ($vendor) {
            $code .= "namespace Vendor\\$name;

/**
 * ${name}Controller seems a cool idea
 */
class ${name}Controller extends \\Vendor\\VendorController
{
";
        } else {
            $code.= "class ${name}Controller extends Controller
 {
 ";
        }

        $code .= "
    /**
     * @var ${name}Controller The class instance.
     * @internal
     */
    protected static \$instance;
";
        if ($model) {
            $code .= "
    /**
     * @var mixed The model instance associated to ${name}Controller
     */
    protected \$model;
    ";
        }

        $code .= "
    /**
     * Returns a ${name}Controller instance, creating it if it did not exist.
     * @return ${name}Controller
     */
    public static function singleton() {
        if (!self::\$instance) {
            \$v = __CLASS__;
            self::\$instance = new \$v;
        }
        return self::\$instance;
    }

    public function __construct() {
        parent::__construct();";
        if ($model) {
            $code .= "
        \$this->model = ${name}Model::singleton();";
        }

        if ($vendor) {
            $code .= "

        /* \$pluginFolder must be the same as the Namespace. So... */
        \$this->pluginFolder = array_pop(explode('\\\\', __NAMESPACE__));";
        }

        $code .= "
    }
";

        if ($model) {
            $code .= "
    /**
     * Returns the instance of the model for this controller
     * @return ${name}Model
     */
    public function getModel() {
        return \$this->model;
    }
";
        }
        $code .= "}";

        echo $code;






        ////////////////////////////////////////





    } elseif (!empty($model)) {
        $code = "<?php

/**
 * ${name}Model goes great with ${name}Controller
 */
";
        if ($vendor) {
            $code .= "namespace Vendor\\$name;

class ${name}Model extends \\Model
{
";
        } else {
            $code.= "class ${name}Model extends Model
 {
 ";
        }

        $code .= "
    /**
     * @var ${name}Model The class instance.
     * @internal
     */
    protected static \$instance;

    /**
     * Returns a ${name}Model instance, creating it if it did not exist.
     * @return ${name}Model
     */
    public static function singleton() {
        if (!self::\$instance) {
            \$v = __CLASS__;
            self::\$instance = new \$v;
        }
        return self::\$instance;
    }
}";

        echo $code;
    }