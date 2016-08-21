<?php

namespace Kernel\Providers;
use Core\Providers\Config;

/**
 * This file manages the routing for incoming URLs. Returns a call (<b>Controller->action()</b>) for the specific route.<br/>
 * To use in conjunction with <b>/src/config/routing.ini</b>, where all the desired routes must be present.
 * @usage
 * The [keys] MUST be unique throughout ALL routing.ini files.<br/>
 * The action omits the "Controller" part of the controller name. <br/>
 * Login@showLogin calls LoginController->showLogin(), \Core\Login@showLogin calls
 * \Core\LoginController->showLogin() and @test calls Controller->test()
 *
 * @example
 * <code>
 * [RoutingKey]
 *     path = <desired_path> ; no leading slash
 *     action = <Controller_Name>@<Action_Name> ; don't write "Controller", just its name
 *     after_login = 1 ; (or 0. OPTIONAL and 0 by default.<br/>
 *                   This means: if on first attempt it has no permission, can the user be auto-redirected after login?)
 *     method = "PUT|POST" ; (GET|PUT|POST|DELETE) OPTIONAL, "|" separated if multiple.
 *                   Will only enter if $_SERVER['REQUEST_METHOD'] matches
 *     condition = '$_SERVER["HTTP_HOST"] == "localhost"' ; OPTIONAL.
 *                   If set, the code will be evaluated (function: eval()) as a further filter
 *     default[animal] = dog
 *     default[sound] = bark ; This is optional! but /zoo/:animal/:sound will match for /zoo, /zoo/<any_animal> and
 *                             /zoo/<any_animal>/<any_sound> all in only one route! (use it for things like /blogpost/:page/:number
 *                             with :page = page and :number = 1 as defaults. Hence /blogpost is page 1 and /blogpost/page/2 is page 2)
 * </code>
 *
 * @package Kernel
 */
class Router {

    /**
     * @var string The request URI for this thread execution. (i.e., the URL in the browser)
     */
    public static $uri;

    /**
     * Searches for a URI match and returns the Controller + Action that dispatch it.
     * @return array ControllerName and Action for requested URI
     */
    public static function matchRoute() {        
        $config = Config::singleton();
        $routing = $config->get('Routing');
        
        self::$uri = substr(strtok($_SERVER['REQUEST_URI'] ?: '/', '?'), strlen(__PATH__) + 1);
        foreach ($routing as $routeKey => $route) {
            if (
                /* You can set a method = get|post|put|delete to further filter. See example */
                (!empty($route['method']) && !in_array($_SERVER['REQUEST_METHOD'], explode('|', strtoupper($route['method'])))) ||
                (!isset($route['path']) /* this means is the [404] Route */)) {
                continue;
            }

            /**
             * If there are defaults set, we replace it in the route.
             * This makes /blog, /blog/1, /blog/123 match the same route if default[page] is set:
             * -------
             * path = /blog/:page
             * default[page] = 1
             */
            $matchesDefault = FALSE;
            if (!empty($route['default'])) {
                $matchesDefault = self::checkForDefaultParams($route);
            }

            /* We replace the placeholders :id, or :name, or :foo for a valid RegEx */
            $pregRoute = preg_replace('(:[a-z0-9_]+)', '([^\/]+)', str_replace('/', '\/', $route['path']));
            $path = '/^' . $pregRoute . '\/?$/';

            if (preg_match($path, self::$uri, $matches) || $matchesDefault) {
                /* If PHP code is added to further filter routing, we evaluate it.
                   And if the condition doesn't match, we skip this route */
                if (isset($route['condition'])) {
                    $condition = FALSE;
                    eval('$condition = ' . $route['condition'] . ";");
                    if (!$condition) {
                        continue;
                    }
                }
                $routeMatches = $matchesDefault ?: $matches;

                /* Route found, so we set the Get params and return the proper Route (key) */
                self::setGetParams($route, $routeMatches);
                return $routeKey;
            }
        }

        /* Not found! Will go to NOT_FOUND_PAGE route */
        return 'NOT_FOUND_PAGE';
    }
    
    /**
     * Sets the $_GET superglobal with the GET parsed parameters. Just like .htaccess
     * @param array $route The route object from the routing file
     * @param array $matches The GET matches in the URL
     */
    private static function setGetParams($route, $matches) {
        /* The :placeholders get added to the $_GET global */
        if (count($matches) > 1) {
            preg_match_all('(:([a-z_]+))', str_replace('/', '\/', $route['path']), $params);
            foreach ($params[1] as $key => $param) {
                if (isset($matches[$key + 1])) {
                    $_GET[$param] = $matches[$key + 1];
                }
            }
        }
        /* The get[foo] parameters on the routing.ini get added to the $_GET superglobal */
        if (!empty($route['get'])) {
            foreach ($route['get'] as $get => $value) {
                $_GET[$get] = $value;
            }
        }
        /* The default values are added to the URL if they are not previously set */
        if (!empty($route['default'])) {
            foreach ($route['default'] as $default => $value) {
                if (!isset($_GET[$default])) {
                    $_GET[$default] = $value;
                }
            }
        }
    }

    /**
     * This function scans a routing rule that <em>contains</em> <b>default[&lt;foo&gt;]</b> parameters and tries to
     * match it against the current route by trying parameters iteratively.
     * Thanks to this function, the following many URIs can be controlled from the same path:
     * <code>
     * // The URIs: /zoo/cat/meow /zoo/cat and /zoo provide cat|meow, cat|bark and dog|bark from the $_GET global
     * //     $_GET['animal'] and $_GET['sound'] respectively
     * [Zoo]
     * path = zoo/:animal/:sound
     * default[animal] = dog
     * default[sound]  = bark
     * action = Foo@Zoo
     *
     * @param array $route The routing info for this iteration of all routing rules
     *
     * @note As you might imagine, adding default parameters slows down the loading a little. Avoid excessive use!
     *
     * @return array|FALSE The found matches (to later replace and add to $_GET) or FALSE if there are no matches
     */
    private static function checkForDefaultParams($route) {
        $fakeRoute = str_replace('/', '\/', $route['path']);
        preg_match_all("/:([a-z0-9_]+)/", $fakeRoute, $keyMatchesAux);
        $keyMatches = array_reverse($keyMatchesAux[1]);
        foreach ($keyMatches as $k) {
            // This means there are no more default values to check
            if (empty($route['default'][$k])) break;

            $fakeRoute = str_replace('\/:' . $k, '', $fakeRoute);
            $pregFakeRoute = '/^' . preg_replace('(:[a-z0-9_]+)', '([^\/]+)', $fakeRoute) . '\/?$/';
            if (preg_match($pregFakeRoute, self::$uri, $matches)) {
                /* Found a match, so we return WHICH parameters do match */
                return $matches;
            }
        }
        /* If the URL doesn't match this route with defaults, it means it's not the right one! */
        return FALSE;
    }
}