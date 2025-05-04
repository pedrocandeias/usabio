<?php
require_once __DIR__ . '/../helpers/translation.php';
class BaseController
{
    protected $pdo;

    // Contexto base de projeto
    protected $projectBase;
    protected $projectTests = [];
    protected $projectParticipants = [];
    protected $projectAssignedUsers = [];
    public $lang = [];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->loadLanguage();
        // Detecta project_id
        if (isset($_GET['controller']) && $_GET['controller'] === 'Project') {
            $projectId = $_GET['id'] ?? null;
        } else {
            $projectId = $_GET['project_id'] ?? $_POST['project_id'] ?? null;
        }
        // Se não tiver, tenta obter via test_id
        if (!$projectId && isset($_GET['id'])) {
            $stmt = $this->pdo->prepare("SELECT project_id FROM tests WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $projectId = $stmt->fetchColumn();
        }

        if ($projectId) {
            $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$projectId]);
            $this->projectBase = $stmt->fetch(PDO::FETCH_ASSOC);
           
            if ($this->projectBase) {
                $this->loadProjectData($projectId);
            }
        }
    }

    private function loadProjectData($projectId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $this->projectTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $this->projectParticipants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare(
            "SELECT m.id, m.username
             FROM project_user pu
             JOIN moderators m ON pu.moderator_id = m.id
             WHERE pu.project_id = ?"
        );
        $stmt->execute([$projectId]);
        $this->projectAssignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectBase()
    {
        return $this->projectBase;
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

    protected function loadLanguage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

    
        $language = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
        $_SESSION['lang'] = $language;
     
        $langFile = __DIR__ . '/../lang/' . $language . '.php';

        $this->lang = file_exists($langFile)
            ? include $langFile
            : include __DIR__ . '/../lang/en.php';
    
        $GLOBALS['lang'] = $this->lang; // Make it available to views and __()
    }
}
