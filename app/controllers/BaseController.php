<?php

class BaseController
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    protected function requireSuperadmin()
    {
        if (empty($_SESSION['is_superadmin'])) {
            echo "Access denied.";
            exit;
        }
    }

    protected function getSetting($key)
    {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn();
    }
}
