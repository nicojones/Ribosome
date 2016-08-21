<?php

namespace Kernel\Dispatch;
use Core\Config;

require_once __ROOT__ . "/app/libs/Mandrill/Mandrill.php";


/**
 * Send an email using Mandrillapp. Set the &lt;key&gt; from <b>/app/config/config.ini</b>
 * @url http://mandrillapp.com
 * @package Kernel
 */
class Mailer {

    /**
     * @var Mailer|false $instance A Mailer instance
     * @internal
     */
    protected static $instance;

    /**
     * @var \Mandrill A Mandrillapp instance, responsible of sending the email.
     */
    var $mail;

    /**
     * @var Config The instance of the Config class.
     */
    protected $config;

    private function __construct() {
        $this->config = Config::singleton();
    }

    /**
     * Returns a Mailer instance, creating it if it did not exist.
     * @return Mailer
     */
    public static function singleton() {
        if (!self::$instance) {
            $v = __CLASS__;
            self::$instance = new $v;
        }
        return self::$instance;
    }

    /**
     * Sends an email using the PHP function, but first setting the appropriate headers, content-types... and <b>attached files</b>.
     * @param mixed $to Send it to whom? can be a string "john&#64;example.com", an array('john&#64;example.com', 'John Smith') or an array('Addresses' => array(array('john&#64;example.com', 'John Smith'), 'peter&#64;example.com', ....))
     * @param string $subject The message subject for the email
     * @param string $message The message body
     * @param mixed $from array('somebody&#64;example.com', 'Foo Bar');
     * @param mixed $replyTo 'somebody&#64;example.com'. The email address to reply to;
     * @param mixed $bcc 'im-secret&#64;example.com'. The email address to bcc to;
     * @param array $files (default array() ) Array with the filenames of the desired files to attach.
     * @throws \Exception:Mandril_Error
     * @throws \Exception
     * @return boolean TRUE if sending was OK, FALSE otherwise. This <b>does not mean</b> the email will be delivered: it just means there was no error when sending it
     * @author Nico Kupfer
     */
    public function sendEmail($to, $subject, $message, $from = [], $replyTo = '', $bcc = '', $files = array()) {
        if (!$to || !$subject || !$message) {
            return FALSE;
        }

        // If only the address is set, we set name=address
        if (is_string($from)) {
            $from = [$from, $from];
        }
        // If only the address is set, we set name=address
        if (is_string($to)) {
            $to = [$to, $to];
        }

        if (empty($from)) {
            $from = $this->config->get('Email', 'FROM');
        }

        try {

            $mandrill = new \Mandrill($this->config->get('Email', 'MANDRILL_KEY'));
            $message = array(
                'html' => $message,
                'text' => $message,
                'subject' => $subject,
                'from_email' => $from[0],
                'from_name' => $from[1],
                'to' => array(
                    array(
                        'email' => $to[0],
                        'name' => $to[1],
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => $replyTo),
                'important' => false,
                'track_opens' => true,
                'track_clicks' => true,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'bcc_address' => $bcc,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp'
            );
            $async = false;
            $ip_pool = 'Main Pool';
            //$send_at = null;// date('Y-m-d H:i:s');
            $result = $mandrill->messages->send($message, $async, $ip_pool, null);
            //var_dump(($result[0]['status'] === 'sent'));
            //var_dump(($result));die;
            return ($result[0]['status'] === 'sent');
        } catch (\Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }
    }
}
