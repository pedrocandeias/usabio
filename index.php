<?php
session_start();

// Base config
require_once __DIR__ . '/config/db.php'; // Adjust to your setup

// Load environment variables
require_once __DIR__ . '/config/config.php';

// Enable error reporting during dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Route parameters
$controllerName = $_GET['controller'] ?? 'Project';
$actionName     = $_GET['action'] ?? 'index';

$controllerClass = $controllerName . 'Controller';
$controllerFile  = __DIR__ . '/app/controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    $controller = new $controllerClass($pdo);

    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        echo "❌ Action '$actionName' not found in $controllerClass.";
    }
} else {
    echo "❌ Controller '$controllerClass' not found.";
}
