<?php
function __($key) {
    global $lang;
    return $lang[$key] ?? $key;
}

function url_with_lang($baseUrl, $params = []) {
    $lang = $_SESSION['lang'] ?? 'en';
    $params['lang'] = $lang;
    return $baseUrl . '?' . http_build_query($params);
}
