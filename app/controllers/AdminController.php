<?php

class AdminController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!($_SESSION['is_admin'] ?? false)) {
            header('Location: /index.php');
            exit;
        }

        $this->pdo = $pdo;
    }

    public function dashboard()
    {
        // Total evaluations
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM evaluations");
        $totalEvaluations = $stmt->fetchColumn();

        // Total responses
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM responses");
        $totalResponses = $stmt->fetchColumn();

        // Avg time per task (non-zero time_spent only)
        $stmt = $this->pdo->query("SELECT AVG(time_spent) FROM responses WHERE time_spent > 0");
        $avgTime = round($stmt->fetchColumn());

        // Distribution of question types
        $stmt = $this->pdo->query("
            SELECT question_type, COUNT(*) as count FROM questions GROUP BY question_type
        ");
        $questionTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
