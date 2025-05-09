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
    protected $projectImage = null;
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


    protected function getMaxProjectsForUserType($userType)
    {
        $defaultLimits = [
            'none' => 0, 
            'normal' => 1,
            'premium' => 3,
            'superpremium' => 9,
        ];
    
        // Always allow superadmins unlimited access
        if ($_SESSION['is_superadmin'] ?? false) {
            return PHP_INT_MAX;
        }
    
         // Fallback to 'normal' if userType is missing or unknown
        $userType = strtolower(trim($userType));
        if (!isset($defaultLimits[$userType])) {
            $userType = 'normal';
        }


        $key = 'max_projects_per_' . strtolower($userType) . '_user';
        $limit = (int) $this->getSetting($key);
    
        return $limit > 0 ? $limit : ($defaultLimits[$userType] ?? 0);
    }
    

    protected function getMaxProjectsPerUser()
{
    return (int) $this->getSetting('max_projects_per_user') ?: 0;
}


public function userCanCreateProject(): bool
{
    // Superadmins podem sempre criar
    if (!empty($_SESSION['is_superadmin'])) {
        return true;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $userType = $_SESSION['user_type'] ?? 'normal';

    // Conta projetos atuais do utilizador
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM projects WHERE owner_id = ?");
    $stmt->execute([$userId]);
    $projectCount = $stmt->fetchColumn();

    // Obtem o limite máximo
    $max = $this->getMaxProjectsForUserType($userType);

    return $projectCount < $max;
}



    protected function userCanAccessProject($projectId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        $isSuperadmin = $_SESSION['is_superadmin'] ?? false;

        if ($isSuperadmin) {
            return true;
        }

        // Verifica se é dono do projeto
        $stmt = $this->pdo->prepare("SELECT owner_id FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $ownerId = $stmt->fetchColumn();

        if ($ownerId == $userId) {
            return true;
        }

        // Verifica se é utilizador atribuído
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM project_user
            WHERE project_id = ? AND moderator_id = ?
        ");
        $stmt->execute([$projectId, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    protected function requireProjectAccess()
    {
        $projectId = $this->projectBase['id'] ?? null;
        if (!$projectId || !$this->userCanAccessProject($projectId)) {
            header("HTTP/1.1 403 Forbidden");
            echo "Access denied.";
            exit;
        }
    }

    protected function userIsProjectAdmin($projectId): bool
{
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        return false;
    }

    // Superadmins têm sempre acesso total
    if (!empty($_SESSION['is_superadmin'])) {
        return true;
    }

    // Verifica se é o dono do projeto
    $stmt = $this->pdo->prepare("SELECT owner_id FROM projects WHERE id = ?");
    $stmt->execute([$projectId]);
    $ownerId = $stmt->fetchColumn();

    if ($ownerId == $userId) {
        return true;
    }

    // Verifica se é moderador com is_admin = 1 no projeto
    $stmt = $this->pdo->prepare("
        SELECT is_admin FROM project_user 
        WHERE project_id = ? AND moderator_id = ? LIMIT 1
    ");
    $stmt->execute([$projectId, $userId]);
    $isAdmin = $stmt->fetchColumn();

    return (int)$isAdmin === 1;
}

}
