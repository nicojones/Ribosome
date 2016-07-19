<?php

namespace Core;

/**
 * Class ExceptionController contains the page shown on Exceptions (<b>throw new Exception($e)</b>).
 * It is called by default on such cases.
 *
 * @hooks
 * <code>
 * ('show_prod_exception', ['e' => $e]) // Called when an exception is thrown on the PROD environment {@see \Core\ExceptionController::showException}
 * ('show_dev_exception', ['e' => $e]) // Called when an exception is thrown on the DEV environment {@see \Core\ExceptionController::showException}
 * </code>
 * @package Core
 */
class ExceptionController extends ParentController {

    /**
     * @var ExceptionController The class instance.
     * @internal
     */
    protected static $instance;

    /**
     * Returns the ExceptionController instance, creating it if it did not exist.
     * @return ExceptionController
     */
    public static function singleton($path = FALSE) {
        if (!self::$instance) {
            $c = __CLASS__;
            self::$instance = new $c($path);
        }
        return self::$instance;
    }
    
    /**
     * Outputs a 500 header. In <b>dev</b> mode and in localhost shows the catched exception with debug information, otherwise a 500 page is shown.
     * @see isLocalServer()
     * @see isProd()
     * @see isDev()
     * @param \Exception $e
     */
    public function showException(\Exception $e) {
        if (isProd() && !isLocalServer()) {
            $this->hooks->do_action('show_prod_exception', ['e' => $e]);
            $this
                ->header(500)
                ->setTitle('500 Internal Server Error')
                ->add('e', $e)
                ->show('exception/exception');
        } else /* isDev() */ {
            $this->hooks->do_action('show_dev_exception', ['e' => $e]);

            $trace = $e->getTrace();

            $this
                ->header(500)
                ->setTitle('Rendering Exception')
                ->add('e', $e)
                ->add('routing', $this->config->get("Routing"))
                ->add('trace', $trace)
                ->addStyle('libs/bootstrap/themes/paper_theme.css')
                ->show('exception/renderingException');
        }
    }
}
    