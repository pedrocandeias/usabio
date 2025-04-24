<?php

function encryptSetting(string $plainText, string $key): string
{
    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plainText, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

function decryptSetting(string $cipherText, string $key): string
{
    if (empty($cipherText)) return '';

    $c = base64_decode($cipherText, true);
    if ($c === false) return '';

    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);

    if (strlen($c) < $ivlen + 32) return '';

    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, 32);
    $ciphertext_raw = substr($c, $ivlen + 32);

    $calculated_hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
    if (!hash_equals($hmac, $calculated_hmac)) {
        return '';
    }

    $original = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return $original ?: '';
}
