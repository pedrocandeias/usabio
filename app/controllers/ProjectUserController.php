<?php
require_once __DIR__ . '/BaseController.php';

class ProjectUserController extends BaseController
{
    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        parent::__construct($pdo);
    }

    public function index()
    {
        $project_id = $_GET['project_id'] ?? 0;
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        // Apenas superadmins e admins
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            echo "Access denied.";
            exit;
        }


        // Carregar dados do projeto
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        // Carregar todos os moderadores
        $stmt = $this->pdo->prepare("SELECT id, username FROM moderators ORDER BY username");
        $stmt->execute();
        $allModerators = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Moderadores já atribuídos
        $stmt = $this->pdo->prepare("SELECT moderator_id FROM project_user WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $assignedModeratorIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'moderator_id');

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => 'Assign Moderators', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/project_users/assign.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Invalid request.";
            exit;
        }

        $project_id = $_POST['project_id'] ?? 0;
        $assignedUsers = $_POST['assigned_users'] ?? [];

        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        // Apagar anteriores
        $stmt = $this->pdo->prepare("DELETE FROM project_user WHERE project_id = ?");
        $stmt->execute([$project_id]);

        // Inserir os novos
        if (!empty($assignedUsers)) {
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $user_id) {
                $stmt->execute([$project_id, $user_id]);
            }
        }

        $_SESSION['toast_success'] = "Moderators assigned successfully!";
        header("Location: /index.php?controller=Project&action=show&id=" . $project_id);
        exit;
    }
}
