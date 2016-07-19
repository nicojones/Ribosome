<?php

namespace Kernel;
use Core\Config;

/**
 * Logs messages to an external database. Configure it from <b>/app/config/config.ini</b>
 *
 * @hooks
 * <code>
 * ('logger_post', $postArray) // Called before posting an error log {@see \Kernel\Logger::sendLog}
 * </code>
 *
 * @package Kernel
 */
class Logger {

    /**
     * @var Logger|false $instance A class instance
     * @internal
     */
    private static $instance = FALSE;

    /**
     * @var \Core\Hooks The instance of the Hooks class.
     */
    protected static $hooks;

    /**
     * Returns a Logger instance, creating it if it did not exist.
     * @return Logger
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    public function __construct() {
        self::$hooks = $GLOBALS['hooks'];
    }
    
    /**
     * Sends an info message to the central logger
     * @param mixed $message The message to save. If it is not string or numeric, it will be converted using var_export($var, TRUE)
     * @param int $errType = 0 An error code
     */
    public static function info($message, $errType = 0) {
        $debug = debug_backtrace();
        $file = $debug[0]['file'];
        $line = $debug[0]['line'];
        if (!(is_string($message) || is_numeric($message))) {
            $message = var_export($message, TRUE);
        }
        self::sendLog('info', $file, $line, $message, $errType);
    }
    
    /**
     * Sends a warn message to the central logger
     * @param mixed $message The message to save. If it is not string or numeric, it will be converted using var_export($var, TRUE)
     * @param int $errType = 0 An error code
     */
    public static function warn($message, $errType = 0) {
        $debug = debug_backtrace();
        $file = $debug[0]['file'];
        $line = $debug[0]['line'];
        if (!(is_string($message) || is_int($message))) {
            $message = var_export($message, TRUE);
        }
        self::sendLog('warn', $file, $line, $message, $errType);
    }
    
    /**
     * Sends an error message to the central logger
     * @param array $error The error to persist
     */
    public static function error ($error) {
        $errType = $error["type"];
        $file = $error["file"];
        $line = $error["line"];
        $message  = $error["message"];
        self::sendLog('error', $file, $line, $message, $errType);
    }
    
    /**
     * Sends log info to the central logger and FB::logs the response
     * @param string $type The type of log ('info', 'warn', 'error')
     * @param string $file The file of the error
     * @param int $line The line number where the error is triggered
     * @param string $message The error or info message
     * @param int $errType The PHP errno
     */
    protected static function sendLog ($type, $file, $line, $message, $errType) {
        $config = Config::singleton()->load('config.ini');
        if ($config->get('Log', 'LOG_ENABLED') == '0') {
            return;
        }

        $id = __ID__;
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'] ?: 'GET';
        $accept = !empty($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'robot';
        $userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'robot';
        $token = $config->get('Log', 'TOKEN');
        
        $postArray = array(
            'type' => $type,
            'file' => $file,
            'line' => $line,
            'mssg' => $message,
            'errtype' => $errType,
            'id' => $id,
            'url' => $url,
            'method' => $method,
            'accept' => $accept,
            'userag' => $userAgent,
            'token' => $token
        );
        $post = http_build_query($postArray);

        self::$hooks->do_action('logger_post', $postArray);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config->get('Log', 'URL'));
        curl_setopt($ch, CURLOPT_POST, count($postArray));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $r = curl_exec($ch);
        curl_close($ch);
        
        if (class_exists('\FB')) {
            if ($type == 'error') {
                \FB::error($r);
            } elseif ($type == 'warn') {
                \FB::warn($r);
            } else {
                \FB::info($r);
            }
        }
    }
}