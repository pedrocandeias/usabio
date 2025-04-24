<?php
// config/config.php

// Load from .env file if it exists
$env = [];
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath, false, INI_SCANNER_TYPED);
}

// Define constants (optional: add fallback)
define('SECRET_KEY', $env['SUPER_SECRET_KEY'] ?? 'changeme-superkey');
define('OPENAI_API_KEY', $env['OPENAI_API_KEY'] ?? '');
define('DEFAULT_LANGUAGE', $env['DEFAULT_LANGUAGE'] ?? 'en');
define('UI_THEME', $env['UI_THEME'] ?? 'default');
define('ENABLE_REGISTRATION', $env['ENABLE_REGISTRATION'] ?? 1);
define('ENABLE_LOGIN', $env['ENABLE_LOGIN'] ?? 1);
define('ENABLE_APIKEY', $env['ENABLE_APIKEY'] ?? 1);
define('ALLOW_REGISTRATION', $env['ALLOW_REGISTRATION'] ?? 1);
define('FEATURE_FLAGS', $env['FEATURE_FLAGS'] ?? '{}');