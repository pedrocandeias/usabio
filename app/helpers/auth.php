<?php
function require_login()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['username'])) {
        header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
        exit;
    }
}

function encryptSetting($value, $secret)
{
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($value, 'aes-256-cbc', $secret, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptSetting($encrypted, $secret)
{
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $secret, 0, $iv);
}

