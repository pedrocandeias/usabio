<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/encryption.php';

class SettingsController extends BaseController
{
    public function index()
    {
        $this->requireSuperadmin();

        // Load settings (if any)
        $stmt = $this->pdo->prepare("SELECT * FROM settings WHERE setting_key = 'openai_api_key' LIMIT 1");
        $stmt->execute();
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settings) {
            // If no row exists, insert default
            $stmt = $this->pdo->prepare("
                INSERT INTO settings (
                    setting_key, setting_value, enable_apikey, enable_registration, enable_login,
                    default_language, ui_theme, feature_flags, allow_registration, updated_at
                ) VALUES (
                    'openai_api_key', '', 0, 0, 0, 'en', 'default', NULL, 1, NOW()
                )
            ");
            $stmt->execute();

            $stmt = $this->pdo->prepare("SELECT * FROM settings WHERE setting_key = 'openai_api_key' LIMIT 1");
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $decrypted = decryptSetting($settings['setting_value'] ?? '', SECRET_KEY);
        $settings['setting_value'] = $decrypted;

        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Settings', 'url' => '', 'active' => true]
        ];

        include __DIR__ . '/../views/settings/index.php';
    }

    public function save()
    {
        $this->requireSuperadmin();

        $apiKeyRaw = $_POST['openai_api_key'] ?? '';
        $encryptedKey = encryptSetting($apiKeyRaw, SECRET_KEY);

        $stmt = $this->pdo->prepare("
            INSERT INTO settings (
                setting_key, setting_value, enable_apikey, enable_registration, enable_login,
                default_language, ui_theme, feature_flags, allow_registration, updated_at
            ) VALUES (
                'openai_api_key', ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                enable_apikey = VALUES(enable_apikey),
                enable_registration = VALUES(enable_registration),
                enable_login = VALUES(enable_login),
                default_language = VALUES(default_language),
                ui_theme = VALUES(ui_theme),
                feature_flags = VALUES(feature_flags),
                allow_registration = VALUES(allow_registration),
                updated_at = NOW()
        ");

        $stmt->execute([
            $encryptedKey,
            isset($_POST['enable_apikey']) ? 1 : 0,
            isset($_POST['enable_registration']) ? 1 : 0,
            isset($_POST['enable_login']) ? 1 : 0,
            $_POST['default_language'] ?? 'en',
            $_POST['ui_theme'] ?? 'default',
            $_POST['feature_flags'] ?? null,
            isset($_POST['allow_registration']) ? 1 : 0
        ]);

        header("Location: /index.php?controller=Settings&action=index&success=settings_updated");
        exit;
    }
}
