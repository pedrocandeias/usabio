<?php
// app/helpers/menu.php

function isMenuActive($controller, $action, $params = [])
{
    $currentController = $_GET['controller'] ?? '';
    $currentAction = $_GET['action'] ?? '';

    if ($currentController !== $controller || $currentAction !== $action) {
        return false;
    }

    foreach ($params as $key => $value) {
        if (($_GET[$key] ?? null) !== $value) {
            return false;
        }
    }

    return true;
}
