<?php

namespace Kernel;

use Kernel\Providers\Router;
use Kernel\Providers\Permission;

use Core\Providers\Config;
use Core\Language;
use Core\Providers\Session;
use Core\Helpers\Hooks;
use Core\Exceptions\Exception;

/**
 * Front logic. Dispatches and manages the input flow thread.<br/>
 * Contains many of all the $hooks present in the framework.
 * @hooks
 * <code>
 * ('After_Hooks_Setup', $hooks) // called right after the constructor
 * ('exec_beforestart', ['controller' => $controllerName, 'action' => $action]) // just after {@see Router::matchRoute}
 * ('permission_unallowed', [
 *     'permission' => $permission,
 *     'controller' => $controllerName,
 *     'action' => $action]) // when it's an unauthorized request
 * ('general_exception', ['e' => $e]) // when/if \Exception is thrown
 * ('exec_afterend', ['controller' => $controllerName, 'action' => $action]) // When execution has finished
 * </code>
 * @package Kernel
 */
class AppKernel {

    /**
     * Initializes the framework.
     * @throws \Exception When a requested <b>Controller</b> or Controller-><b>action</b>() are requested but do not exist.
     */
    public static function init() {
        
        /**
         * We define the encoding
         */
        //header('Charset: UTF-8');

        /**
         * We start the session
         */
        session_start();

        /**
         * Include the core logic and optional user-set libraries
         */
        $includes = parse_ini_file(__ROOT__ . '/app/config/required.ini', TRUE);
        foreach ($includes as $i) require_once __ROOT__ . '/app/' . $i;

        /**
         * Compute running time
         * You can use clock_end() and clock_time() to compute execution times.
         * See support_functions.php for more info.
         */
        clock_start();

        /**
         * Initialize hooks
         */
//        $hooks = $GLOBALS['hooks'] = new Hooks();
        $hooks = Hooks::init();
        $hooks->do_action('After_Hooks_Setup', $hooks);

        /**
         * Load config files
         */
        $config = Config::singleton()
            ->load('config.ini')
            ->load('routing.ini', FALSE, 'Routing')
            ->load('permissions.ini', FALSE, 'Permissions')
            ->load(__ROOT__ . '/src/resources/routing/permissions.ini', TRUE, 'Permissions')
            ->load(__ROOT__ . '/src/resources/routing/routing.ini', TRUE, 'Routing')
            ->loadVendors();

        /**
         * Set the language:
         */
        Language::singleton()->set();

        /**
         * Get called action
         */
        $routeKey = Router::matchRoute();
        $route = $config->get('Routing', $routeKey); 
        list($controllerName, $action) = explode('@', $route['action']);

        /**
         * We run the hook 'exec_beforestart' to allow for preloads.
         */
        $hooks->do_action('exec_beforestart', ['controller' => $controllerName, 'action' => $action]);
        
        /**
         * Call requested action process
         */
        try {
            $controllerClass = $controllerName . 'Controller';
            if (!class_exists($controllerClass) && !class_exists($controllerName)) {
                throw new \Exception(str_replace('[[CLASS]]', $controllerName, $config->get('Exceptions', 'CLASS_NOT_FOUND')));
            }
            if (!method_exists($controllerClass, $action)) {
                throw new \Exception(str_replace(
                    ['[[CLASS]]', '[[METHOD]]'],
                    [$controllerClass, $action],
                    $config->get('Exceptions', 'METHOD_NOT_FOUND')
                ));
            }
            /**
             * Check action permission
             */
            $permission = Permission::singleton()->checkPermission($controllerName, $action);
            if (!$permission['allowed']) {

                /**
                 * We execute 'permission_unallowed' hook, for double-control on unauthorized requests.
                 */
                $hooks->do_action(
                    'permission_unallowed', [
                        'permission' => $permission,
                        'controller' => $controllerName,
                        'action' => $action]);
                /**
                 * With this we ensure we don't redirect the user to a /ajax/<something>, for instance
                 */
                if (!empty($route['after_login']) && $route['after_login'] == TRUE) {
                    Session::setAfterLogin($_SERVER['REQUEST_URI']);
                }

                /**
                 * Throw unallowed request
                 */
                $throwTo = $permission['throw_to'];
                if (is_array($throwTo)) {
                    $controllerClass = $throwTo[0];
                    $action = $throwTo[1];
                } else {
                    $location = __PATH__ . '/' . $config->get('Routing', $throwTo)['path'];
                    if (isAjax()) {
                        die(json_encode([
                            'success' => 0,
                            'responseData' => [
                                'message' => 'Please login to continue',
                                'redirect' => $location]]));
                    } else {
                        header('Location: ' . $location);
                        die;
                    }
                }
            }

            /**
             * Call requested action
             */
            $controller = $controllerClass::singleton();
            $response = $controller->{$action}();

        } catch (\Exception $e) {
            $hooks->do_action('general_exception', ['e' => $e]);
            /**
             * Catch the exception if something's not OK
             */
            Exception::singleton()->showException($e);
        }

        /* TOTAL time execution */
        clock_end();

        /**
         * We run the hook 'exec_afterend'.
         */
        $hooks->do_action('exec_afterend', ['controller' => $controllerName, 'action' => $action]);

        // echo clock_time() if you want
    }
}