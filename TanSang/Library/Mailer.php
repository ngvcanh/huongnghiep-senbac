<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once K_ROOT .  K_DIR_PLUGIN . '/PHPMailer/src/Exception.php';
require_once K_ROOT .  K_DIR_PLUGIN . '/PHPMailer/src/PHPMailer.php';
require_once K_ROOT .  K_DIR_PLUGIN . '/PHPMailer/src/SMTP.php';

class Mailer{

    private $mailer;
    private $error = true;

    function __construct($subject, $body, array $from, array $recipient, array $settings, 
        $altBody = '', array $attachs = [], array $options = []){
        $this->mailer = new PHPMailer();

        $this->mailer->SMTPDebug = 2;
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;

        $this->setSettings($settings);
        $this->setFrom($from);
        $this->setRecipient($recipient);
        $this->setCC($options);
        $this->setBCC($options);
        $this->setReplyTo($options);

        $this->setAttachment($attachs);

        $this->mailer->isHTML(true);
        $this->setSubject($subject);
        $this->setBody($body);
        $this->setAltBody($altBody);

        $this->send();
    }

    private function send(){
        if (!$this->error) $this->mailer->send();
    }

    private function setSubject($subject){
        $this->error = true;
        if (is_string($subject)){
            $this->mailer->Subject = $subject;
            $this->error = false;
        }
    }

    private function setBody($body){
        $this->error = true;
        if (is_string($body)){
            $this->mailer->Body = $body;
            $this->error = false;
        }
    }

    private function setAltBody($altBody){
        if (is_string($altBody)) $this->mailer->AltBody = $altBody;
    }

    private function setSettings(array $settings){
        $this->error = true;
        
        if (!isset($settings['host']) || !is_string($settings['host'])) return;
        $this->mailer->Host = $settings['host'];

        if (!isset($settings['user']) || !$this->validEmail($settings['user'])) return;
        $this->mailer->Username = $settings['user'];

        if (!isset($settings['pass']) || !!is_string($settings['pass'])) return;
        $this->mailer->Password = $settings['pass'];

        $this->error = false;
    }

    private function setFrom(array $from){
        $this->error = true;
        if (!isset($from[0]) || !isset($from[1])) return;
        if (!$this->validEmail($from[0]) || !is_string($from[1])) return;
        $this->mailer->setFrom($from[0], $from[1]);
        $this->error = false;
    }

    private function setRecipient(array $recipient){
        $this->error = true;
        foreach($recipient as $reci){
            if (is_array($reci) && isset($reci[0]) && isset($reci[1])){
                $email = $reci[0];
                $name = $reci[1];

                if ($this->validEmail($email) && is_string($name)){
                    $this->mailer->addAddress($email, $name);
                    $this->error = false;
                }
            }
        }
    }

    private function setReplyTo(array $options){
        if (!isset($options['replyTo'])) return;
        $replayTo = $options['replyTo'];

        foreach($replayTo as $reply){
            if (is_array($reply) && isset($reply[0])){
                $email = $reci[0];

                if ($this->validEmail($email)){
                    $name = (isset($reply[1]) && is_string($reply[1])) ? $reply[1] : '';
                    $this->mailer->addReplyTo($email, $name);
                }
            }
        }
    }

    private function setCC(array $options){
        if (!isset($options['cc'])) return;
        $ccs = $options['cc'];

        foreach($ccs as $cc){
            if ($this->validEmail($cc)) $this->mailer->addCC($cc);
        }
    }

    private function setBCC(array $options){
        if (!isset($options['bcc'])) return;
        $bccs = $options['bcc'];

        foreach($bccs as $bcc){
            if ($this->validEmail($bcc)) $this->mailer->addCC($bcc);
        }
    }

    private function setAttachment(array $attachs){
        foreach($attachs as $attach){
            if (is_string($attach)) $this->mailer->addAttachment($attach);
            else if (is_array($attach)){
                if (isset($attach[0]) && is_string($attach[0])){
                    $path = $attach[0];
                    $name = (isset($attach[1]) && is_string($attach[1])) ? $attach[1] : NULL;
                    $this->mailer->addAttachment($path, $name);
                }
            }
        }
    }

    private function validEmail($email){
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}