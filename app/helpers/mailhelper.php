<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php'; // Ou caminho alternativo

class MailHelper
{
    public static function sendInviteEmail($toEmail, $projectTitle, $registerUrl)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP com Mailpit (ou Mailhog)
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->SMTPAuth = false;
            $mail->SMTPSecure = false;

            $mail->setFrom('no-reply@usabio.ddev.site', 'Usabio');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = "You're invited to join a project on Usabio";
            $mail->Body = "
                <p>Hello,</p>
                <p>You've been invited to participate as a moderator in the project <strong>$projectTitle</strong>.</p>
                <p><a href=\"$registerUrl\">Click here to register</a></p>
                <p>If you already have an account, you can ignore this email.</p>
            ";
            $mail->AltBody = "You're invited to join the project '$projectTitle'. Visit: $registerUrl";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email invite failed: {$mail->ErrorInfo}");
            return false;
        }
    }
}
