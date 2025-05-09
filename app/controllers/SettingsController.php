<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/encryption.php';
require_once __DIR__ . '/../helpers/mailhelper.php';

class SettingsController extends BaseController
{
    public function index()
    {
        $this->requireSuperadmin();
    
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM settings");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
        // Decriptar chave OpenAI (caso exista)
        require_once __DIR__ . '/../helpers/encryption.php';
        $rows['openai_api_key'] = decryptSetting($rows['openai_api_key'] ?? '', SECRET_KEY);
    
        $settings = $rows;
    
        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Settings', 'url' => '', 'active' => true]
        ];
    
        include __DIR__ . '/../views/settings/index.php';
    }
    
    public function save()
    {
        $this->requireSuperadmin();
    
        require_once __DIR__ . '/../helpers/encryption.php';
    
        $updates = [
            'openai_api_key' => encryptSetting($_POST['openai_api_key'] ?? '', SECRET_KEY),
            'enable_ai_features' => isset($_POST['enable_ai_features']) ? '1' : '0',
            'enable_user_registration' => isset($_POST['enable_user_registration']) ? '1' : '0',
            'enable_login' => isset($_POST['enable_login']) ? '1' : '0',
            'allow_public_registration' => isset($_POST['allow_registration']) ? '1' : '0',
            'default_language' => $_POST['default_language'] ?? 'en',
            'ui_theme' => $_POST['ui_theme'] ?? '',
            'feature_flags_json' => $_POST['feature_flags'] ?? '{}',
            'mailserver_host' => $_POST['mailserver_host'] ?? '',
            'mailserver_port' => $_POST['mailserver_port'] ?? '',
            'mailserver_username' => $_POST['mailserver_username'] ?? '',
            'mailserver_password' => $_POST['mailserver_password'] ?? '',
            'mailserver_encryption' => $_POST['mailserver_encryption'] ?? '',
            'test_email' => $_POST['test_email'] ?? '',
            'platform_base_url' => $_POST['platform_base_url'] ?? '',
        ];
    
        foreach ($updates as $key => $value) {
            $stmt = $this->pdo->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
            ");
            $stmt->execute([$key, $value]);
        }
    
        header("Location: /index.php?controller=Settings&action=index&success=settings_updated");
        exit;
    }

    public function testEmail()
{
    $this->requireSuperadmin();

    $to = $_POST['test_email'] ?? null;
    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        header("Location: /index.php?controller=Settings&action=index&error=invalid_email");
        exit;
    }

    $smtp = [];
    $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM settings");
    foreach ($stmt->fetchAll(PDO::FETCH_KEY_PAIR) as $key => $value) {
        $smtp[$key] = $value;
    }

    $error = '';
    $success = MailHelper::sendTestEmail($to, $this->pdo, $error);

    if ($success) {
        header("Location: /index.php?controller=Settings&action=index&email_success=email_test_sent");
    } else {
        // Envia o erro como query string (podes usar sessão se preferires mais segurança)
        header("Location: /index.php?controller=Settings&action=index&email_error=" . urlencode("Email failed: $error"));
    }

    exit;
}

}
