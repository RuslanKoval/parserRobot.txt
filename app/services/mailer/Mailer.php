<?php

namespace app\services\mailer;

use app\helpers\UrlHelper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer
{
    public static function sendConfirmMail($name = '', $email, $code)
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
        $mailerSettings = $settings['mailer'];

        $registrationLink = UrlHelper::getBaseUrl().'confirm?token='.$code;

        $mail = new PHPMailer(true);
        try {

            $mail->SMTPDebug = 1;
    //        $mail->isSMTP();
            $mail->Host = $mailerSettings['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailerSettings['username'];
            $mail->Password = $mailerSettings['password'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port = $mailerSettings['port'];


            $mail->setFrom($mailerSettings['username']);
            $mail->addAddress($email);


            $mail->isHTML(true);
            $mail->Subject = 'Confirm registration';
            $mail->Body    = "<h1>Hello $name </h1>Confirm registration please follow this <a href=\"$registrationLink\">link</a>";



            $mail->send();

        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

    }
}