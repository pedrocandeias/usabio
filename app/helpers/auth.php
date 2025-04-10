<?php
function require_login()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['username'])) {
        header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
        exit;
    }
}
