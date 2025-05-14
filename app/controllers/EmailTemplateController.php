<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/mailhelper.php';


class EmailTemplateController extends BaseController
{
    public function index()
    {
        $this->requireSuperadmin();

        $stmt = $this->pdo->query("SELECT * FROM email_templates ORDER BY template_key ASC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Email Templates', 'url' => '', 'active' => true]
        ];

        include __DIR__ . '/../views/email_templates/index.php';
    }

    public function edit()
    {
        $this->requireSuperadmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /index.php?controller=EmailTemplate&action=index&error=missing_id');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            header('Location: /index.php?controller=EmailTemplate&action=index&error=not_found');
            exit;
        }

        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Email Templates', 'url' => '/index.php?controller=EmailTemplate&action=index', 'active' => false],
            ['label' => 'Edit Template', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/email_templates/edit.php';
    }

    public function update()
    {
        $this->requireSuperadmin();

        $id = $_POST['id'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if (!$id || !$subject || !$body) {
            header('Location: /index.php?controller=EmailTemplate&action=edit&id=' . $id . '&error=missing_fields');
            exit;
        }

        $stmt = $this->pdo->prepare("
            UPDATE email_templates 
            SET subject = ?, body = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$subject, $body, $id]);

        header('Location: /index.php?controller=EmailTemplate&action=index&success=updated');
        exit;
    }

    public function testSingle()
{
    $this->requireSuperadmin();

    $templateKey = $_GET['template'] ?? '';
    if (!$templateKey) {
        $_SESSION['toast_error'] = "Template not specified.";
        header('Location: /index.php?controller=EmailTemplate&action=index');
        exit;
    }

    // ✅ Get test email from settings
    $toEmail = $this->getSetting('test_email');
    if (!$toEmail) {
        $_SESSION['toast_error'] = "No test email defined in settings (key: test_email).";
        header('Location: /index.php?controller=EmailTemplate&action=index');
        exit;
    }

    // Dummy placeholders
    $placeholders = [
        'fullname' => 'Test User',
        'username' => 'testuser',
        'login_url' => MailHelper::getLoginUrl($this->pdo),
        'platform_name' => $this->getSetting('platform_name') ?? 'TestFlow',
        'support_email' => $this->getSetting('support_email') ?? 'support@testflow.design',
        'project_title' => 'Example Project',
        'link' => 'https://example.com'
    ];

    $template = load_email_template($this->pdo, $templateKey, $placeholders);

    if (!$template) {
        $_SESSION['toast_error'] = "Template not found.";
        header('Location: /index.php?controller=EmailTemplate&action=index');
        exit;
    }

    // Send email
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $smtp = MailHelper::getSmtpConfig($this->pdo);
    $mail->isSMTP();
    $mail->Host = $smtp['mailserver_host'] ?? 'localhost';
    $mail->Port = $smtp['mailserver_port'] ?? 1025;
    $mail->SMTPAuth = !empty($smtp['mailserver_username']);
    $mail->Username = $smtp['mailserver_username'] ?? '';
    $mail->Password = $smtp['mailserver_password'] ?? '';
    $mail->SMTPSecure = $smtp['mailserver_encryption'] ?? '';

    $defaults = MailHelper::getEmailDefaults($this->pdo);
    $mail->setFrom($defaults['from_email'], $defaults['from_name']);
    $mail->addAddress($toEmail);
    $mail->isHTML(true);
    $mail->Subject = $template['subject'];
    $mail->Body = $template['body'];
    $mail->AltBody = strip_tags($template['body']);

    try {
        $mail->send();
        $_SESSION['toast_success'] = "✅ Test email sent to $toEmail.";
    } catch (Exception $e) {
        $_SESSION['toast_error'] = "❌ Send failed: " . $mail->ErrorInfo;
    }

    header('Location: /index.php?controller=EmailTemplate&action=index');
    exit;
}


}
