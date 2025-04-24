<?php 
function getOpenAIKey(PDO $pdo): ?string {
    require_once __DIR__ . '/encryption.php';

    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'openai_api_key'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? decryptSetting($row['setting_value'], SECRET_KEY) : null;
}
