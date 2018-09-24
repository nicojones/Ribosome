<?php

namespace Core\Helpers;
use Core\Providers\Session;

/**
 * Manages flash messages.
 *
 * Flash messages are $_SESSION objects that can only be read once - and in doing so get destroyed.
 *
 * @package Core
 */
class Flash
{

    private static $defaultMessage = 'Success!';
    private static $defaultMessageType = 'success';

    /**
     * Sets a <b>flash message</b> with a <b>message type</b>.
     * @param string $message The message to display
     * @param string $messageType The type of message: success|info|warning|danger
     * @return array The data: [message => $message, type => $type]
     */
    public static function set($message = null, $messageType = null) {
        $message     = $message     ?: self::$defaultMessage;
        $messageType = $messageType ?: self::$defaultMessageType;

        if (!in_array($messageType, ['success', 'info', 'warning', 'danger'])) {
            $messageType = 'success';
        }

        $flash = ['message' => $message, 'type' => $messageType];

        Session::set('flash_message', $flash);
        return $flash;
    }

    /**
     * Alias for Flash::flush(): Syntactic sugar to retrieve and delete the message
     * @alias Flash::flush()
     * @see \Core\Flash::flush()
     */
    public static function get() {
        return self::flush();
    }

    /**
     * Gets the value of the flash <b>flash message</b> and the <b>flash message type</b>.
     * @return array|bool The whole data if there's a flash message, <b>FALSE</b> otherwise. Flushes de data afterwards.
     */
    public static function flush() {
        if ($flashMessage = Session::get('flash_message')) {
            Session::clean('flash_message');
            return $flashMessage;
        }
        return FALSE;
    }

    /**
     * Returns whether there's a flash message or not TRUE|FALSE
     * @return bool
     */
    public static function hasMessage() {
        return !empty(Session::get('flash_message'));
    }

    /**
     * @param bool|false $flush Whether to flush the data or not.
     * @return string The message.
     */
    public static function message($flush = false) {
        $flashMessage = Session::get('flash_message');

        if ($flush) {
            self::flush();
        }
        return $flashMessage['message'];
    }

    /**
     * @param bool|false $flush Whether to flush the data or not.
     * @return string The type of message.
     */
    public static function type($flush = false) {
        $flashMessage = Session::get('flash_message');

        if ($flush) {
            self::flush();
        }
        return $flashMessage['type'];
    }


}
