<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';


class MailHelper
{
    public static function sendInviteEmail($toEmail, $projectTitle, $registerUrl, $pdo)
    {
        $smtp = self::getSmtpConfig($pdo);
        $mail = new PHPMailer(true);
    
        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host = $smtp['mailserver_host'] ?? 'localhost';
            $mail->Port = $smtp['mailserver_port'] ?? 1025;
            $mail->SMTPAuth = !empty($smtp['mailserver_username']);
            $mail->Username = $smtp['mailserver_username'] ?? '';
            $mail->Password = $smtp['mailserver_password'] ?? '';
            $mail->SMTPSecure = $smtp['mailserver_encryption'] ?? '';
            $defaults = self::getEmailDefaults($pdo);
            $mail->setFrom($defaults['from_email'], $defaults['from_name']);
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
    
            // Carregar o template e substituir placeholders
            $login_url = MailHelper::getLoginUrl($pdo);

            $template = load_email_template($pdo, 'invite_email', [
                'project_title' => $projectTitle,
                'link' => $registerUrl,
                'login_url' => $login_url,
                'email' => $toEmail,
            ]);
    
            if (!$template) {
                // error_log("Missing template: invite_email");
                return false;
            }
    
            $mail->Subject = $template['subject'];
            $mail->Body = $template['body'];
            $mail->AltBody = strip_tags($template['body']);
    
            $mail->send();
            return true;
    
        } catch (Exception $e) {
           //  error_log("Invite email failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendRegistrationConfirmation($toEmail, $fullname, $pdo)
{
    $smtp = self::getSmtpConfig($pdo);
    $mail = new PHPMailer(true);

    try {
        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = $smtp['mailserver_host'] ?? 'localhost';
        $mail->Port = $smtp['mailserver_port'] ?? 1025;
        $mail->SMTPAuth = !empty($smtp['mailserver_username']);
        $mail->Username = $smtp['mailserver_username'] ?? '';
        $mail->Password = $smtp['mailserver_password'] ?? '';
        $mail->SMTPSecure = $smtp['mailserver_encryption'] ?? '';

        $defaults = self::getEmailDefaults($pdo);
        // error_log("Email defaults: " . json_encode($defaults));
        $mail->setFrom($defaults['from_email'], $defaults['from_name']);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);

        // Carregar template
        $template = load_email_template($pdo, 'registration_confirmation', [
            'fullname' => $fullname,
            'email' => $toEmail
            // login_url e platform_name são automáticos
        ]);
        // error_log("Loaded template: " . json_encode($template));

        if (!$template) {
            // error_log("Missing template: registration_confirmation");
            return false;
        }

        $mail->Subject = $template['subject'];
        $mail->Body = $template['body'];
        $mail->AltBody = strip_tags($template['body']);
        // error_log("subject:" . $template['subject']);
        // error_log("body:" . $template['body']);
        $mail->send();
        return true;

    } catch (Exception $e) {
       //  error_log("Registration confirmation failed: " . $mail->ErrorInfo);
        return false;
    }
}



    public static function sendTestEmail($to, $pdo, &$error = null)
    {
        $smtp = self::getSmtpConfig($pdo);
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $smtp['mailserver_host'] ?? 'localhost';
            $mail->Port = $smtp['mailserver_port'] ?? 1025;
            $mail->SMTPAuth = !empty($smtp['mailserver_username']);
            $mail->Username = $smtp['mailserver_username'] ?? '';
            $mail->Password = $smtp['mailserver_password'] ?? '';
            $mail->SMTPSecure = $smtp['mailserver_encryption'] ?? '';

            $defaults = self::getEmailDefaults($pdo);
            $mail->setFrom($defaults['from_email'], $defaults['from_name']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = 'Test Email from TestFlow';
            $mail->Body = '<p>This is a test email confirming your mailserver settings are working.</p>';
            $mail->AltBody = 'This is a test email from TestFlow.';

            $mail->send();
            return true;
        } catch (Exception $e) {
            $error = $mail->ErrorInfo;
            // error_log("Test email failed: " . $mail->ErrorInfo);
            return false;
        }
    }


    public static function getSmtpConfig($pdo)
    {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'mailserver_%'");
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (Exception $e) {
            // error_log("Failed to load SMTP settings: " . $e->getMessage());
            return [];
        }
    }

    public static function getEmailDefaults($pdo)
    {
        $stmt = $pdo->query("
            SELECT setting_key, setting_value 
            FROM settings 
            WHERE setting_key IN ('noreplymail', 'platform_name')
        ");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        // error_log("Email defaults loaded: " . implode(', ', array_keys($settings)));
    
        return [
            'from_email' => $settings['noreplymail'] ?? 'noreply@testflow.design',
            'from_name' => $settings['platform_name'] ?? 'TestFlow'
        ];
    }
    

    public static function getLoginUrl($pdo)
    {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'platform_base_url'");
    $stmt->execute();
    $base_url = $stmt->fetchColumn() ?: 'https://usabio.ddev.site';

    return rtrim($base_url, '/') . '/index.php?controller=Auth&action=login';
    }

    public static function sendConfirmationEmail($toEmail, $fullname, $confirmationUrl, $pdo)
{
    $smtp = self::getSmtpConfig($pdo);
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtp['mailserver_host'] ?? 'localhost';
        $mail->Port = $smtp['mailserver_port'] ?? 1025;
        $mail->SMTPAuth = !empty($smtp['mailserver_username']);
        $mail->Username = $smtp['mailserver_username'] ?? '';
        $mail->Password = $smtp['mailserver_password'] ?? '';
        $mail->SMTPSecure = $smtp['mailserver_encryption'] ?? '';

        $defaults = self::getEmailDefaults($pdo);
        $mail->setFrom($defaults['from_email'], $defaults['from_name']);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);

        $template = load_email_template($pdo, 'email_confirmation_request', [
            'fullname' => $fullname,
            'confirmation_link' => $confirmationUrl
        ]);

        if (!$template) {
            error_log("Missing template: email_confirmation_request");
            return false;
        }

        $mail->Subject = $template['subject'];
        $mail->Body = $template['body'];
        $mail->AltBody = strip_tags($template['body']);

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Confirmation email failed: " . $mail->ErrorInfo);
        return false;
    }
}


} // mailhelper


function load_email_template($pdo, string $template_key, array $placeholders = []): ?array
{
    // error_log("loading this function");
    $stmt = $pdo->prepare("SELECT subject, body FROM email_templates WHERE template_key = ?");
    $stmt->execute([$template_key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    $subject = $row['subject'];
    $body = $row['body'];

    // Todas as tags detectadas no subject ou body
    preg_match_all('/\{\{(.*?)\}\}/', $subject . $body, $matches);
    $tagsFound = array_unique($matches[1]);
    // error_log("Tags found: " . implode(', ', $tagsFound));
    // Procurar apenas as tags não preenchidas manualmente
    $missingTags = array_diff($tagsFound, array_keys($placeholders));
   //  error_log("Missing tags: " . implode(', ', $missingTags));
    if (!empty($missingTags)) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        // error_log("Settings loaded: " . implode(', ', array_keys($settings)));
        foreach ($missingTags as $tag) {
            switch ($tag) {
                case 'login_url':
                    $base = $settings['platform_base_url'] ?? 'https://usabio.ddev.site';
                    $placeholders['login_url'] = rtrim($base, '/') . '/index.php?controller=Auth&action=login';
                    break;

                case 'platform_name':
                    $placeholders['platform_name'] = $settings['platform_name'] ?? 'TestFlow';
                    break;

                case 'support_email':
                    $placeholders['support_email'] = $settings['support_email'] ?? 'support@usabio.ddev.site';
                    break;

                // podes adicionar mais aqui
            }
        }
        
    }

    // Substituição final
    foreach ($placeholders as $key => $value) {
        $tag = '{{' . $key . '}}';
        $subject = str_replace($tag, $value, $subject);
        $body = str_replace($tag, $value, $body);
    }
    // ("EMAIL TEMPLATE SUBJECT: $subject");
    // error_log("EMAIL TEMPLATE BODY: $body");
    
    return [
        'subject' => $subject,
        'body' => $body
    ];
}
