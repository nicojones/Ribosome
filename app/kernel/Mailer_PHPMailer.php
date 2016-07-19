<?php

namespace Kernel;

use Core\Config;

require_once __ROOT__ . "/app/libs/PHPMailer/PHPMailerAutoload.php";

/**
 * Send emails from your own server with the library PHPMailer
 * @package Kernel
 */
class Mailer_PHP {

    /**
     * @var Mailer_PHP|false $instance A Mailer_PHP instance
     * @internal
     */
    private static $instance;

    /**
     * @var \PHPMailer An instance of PHPMailer
     */
    var $mail;

    /**
     * Returns a Mailer_PHP instance, creating it if it did not exist.
     * @return Mailer_PHP
     */
    public static function singleton()
    {
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
    * @param mixed $from ( 'somebody&#64;example.com' ) The email address from the sender or an array('somebody&#64;example.com', 'Foo Bar');
    * @param mixed $replyTo ( 'somebody&#64;example.com', 'foo&#64;example.com', ... ) The email address to reply to or an array array(array('somebody&#64;example.com', 'Foo Bar'), array(...));
    * @param array $files (default array() ) Array with the filenames of the desired files to attach.
    * @return boolean TRUE if sending was OK, FALSE otherwise. This <b>does not mean</b> the email will be delivered: it just means there was no error when sending it
    * @author Nico Kupfer
    */
    public function sendEmail($to, $subject, $message, $from, $replyTo = array(), $files = array())
    {
        if (!$to || !$subject || !$message || !$from) {
            return FALSE;
        }

        $this->mail = new \PHPMailer();
        $this->mail->CharSet = 'UTF-8';

        $config = Config::singleton();
        $SMTP = $config->get('SMTP');
        var_dump($SMTP);
        // We specify whether it's SMTP
        if ($SMTP && array_key_exists('Auth', $SMTP) && $SMTP['Auth'] == 1) {
            $this->mail->isSMTP();
            $this->mail->Debugoutput = 'echo';
            $this->mail->SMTPDebug = 10; //(int)$SMTP['Debug'];

            // We specify the SMTP credentials
            $this->mail->SMTPAuth = TRUE;
            $this->mail->Username = $SMTP['Username'];
            $this->mail->Password = $SMTP['Password'];

            $this->mail->Host = $SMTP['Host'];
            $this->mail->Port = $SMTP['Port'];
        }

        // We add the From
        if (is_array($from)) {
            $this->mail->setFrom($from[0], $from[1]);
        } else {
            $this->mail->setFrom($from);
        }

        // We add the addresses
        if (is_array($to) && array_key_exists('Addresses', $to)) {
            foreach ($to['Addresses'] as $address) {
                if (is_array($address)) {
                    $this->mail->addAddress($address[0], $address[1]);
                } else {
                    $this->mail->addAddress($address);
                }
            }
        } elseif (is_array($to)) {
            $this->mail->addAddress($to[0], $to[1]);
        } else {
            $this->mail->addAddress($to);
        }

        foreach ($replyTo as $reply) {
            if (is_array($reply) && !empty($reply[1])) {
                $this->mail->addReplyTo($reply[0], $reply[1]);
            } else {
                $this->mail->addReplyTo($reply);
            }
        }

        $this->mail->msgHTML = $message;
        $this->mail->WordWrap = 50;
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = wordwrap($message, 70, "\r\n");

        if ($files) {
            foreach ($files as $file) {
                $this->mail->addAttachment($file);
            }
        }

        $sent = $this->mail->send();
        if (!$sent) {
            echo $this->mail->ErrorInfo;
            return FALSE;
        } else {
            return TRUE;
        }

    }
}