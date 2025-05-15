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
        $project_id = $_GET['project_id'] ?? null;
    
        // Carrega o projeto
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        // Moderadores já atribuídos + is_admin
        $stmt = $this->pdo->prepare("
            SELECT m.*, pu.is_admin 
            FROM moderators m
            INNER JOIN project_user pu ON pu.moderator_id = m.id
            WHERE pu.project_id = ?
        ");
        $stmt->execute([$project_id]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assignedModeratorIds = array_column($assignedUsers, 'id');
    
        // Todos os moderadores (para dropdown de convite)
        $stmt = $this->pdo->prepare("SELECT * FROM moderators ORDER BY username");
        $stmt->execute();
        $allModerators = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Convites pendentes (moderadores registados)
        require_once __DIR__ . '/../models/ProjectInvite.php';
        $inviteModel = new ProjectInvite($this->pdo);
        $pendingInvites = $inviteModel->getPendingInvitesForProject($project_id);
        $pendingInviteModeratorIds = array_column($pendingInvites, 'moderator_id');
    
        // Construir lista de moderadores visíveis
        $visibleModerators = [];
    
        foreach ($assignedUsers as $mod) {
            $mod['status'] = 'assigned';
            $visibleModerators[$mod['id']] = $mod;
        }
    
        foreach ($pendingInvites as $invite) {
            $moderator_id = $invite['moderator_id'];
    
            foreach ($allModerators as $mod) {
                if ($mod['id'] == $moderator_id) {
                    $mod['status'] = 'pending';
                    $mod['invite_id'] = $invite['id'];
                    $visibleModerators[$moderator_id] = $mod;
                    break;
                }
            }
        }
    
        // Convites por email (utilizadores não registados)
        $stmt = $this->pdo->prepare("
            SELECT * FROM pending_invite_emails
            WHERE project_id = ? AND status = 'sent'
        ");
        $stmt->execute([$project_id]);
        $pendingEmailInvites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($pendingEmailInvites as $entry) {
            $visibleModerators['email:' . $entry['email']] = [
                'id' => null,
                'email' => $entry['email'],
                'status' => 'email_sent'
            ];
        }
    
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Users', 'url' => '', 'active' => true],
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

        if (!$this->userIsProjectAdmin($project_id)) {
            echo "Access denied.";
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

    public function delete()
    {
        $project_id = $_GET['project_id'] ?? null;
        $user_id = $_GET['user_id'] ?? null;

        if (!$project_id || !$user_id) {
            header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&error=missing_data');
            exit;
        }

        if (!$this->userIsProjectAdmin($project_id)) {
            echo "Access denied.";
            exit;
        }
        
        $stmt = $this->pdo->prepare("
            DELETE FROM project_user 
            WHERE project_id = ? AND moderator_id = ?
        ");
        $stmt->execute([$project_id, $user_id]);

        // Remove any old accepted project_invites
        $stmt = $this->pdo->prepare("
            DELETE FROM project_invites 
            WHERE project_id = ? AND moderator_id = ? AND status = 'accepted'
        ");
        $stmt->execute([$project_id, $user_id]);


        $_SESSION['toast_success'] = "Moderator removed successfully!";
        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&success=moderator_removed');
        exit;
    }

    public function promote()
{
    $project_id = $_POST['project_id'] ?? null;
    $moderator_id = $_POST['moderator_id'] ?? null;

    if (!$project_id || !$moderator_id) {
        echo "Missing data.";
        exit;
    }

    if (!$this->userIsProjectAdmin($project_id)) {
        echo "Access denied.";
        exit;
    }

    $stmt = $this->pdo->prepare("
        UPDATE project_user 
        SET is_admin = 1 
        WHERE project_id = ? AND moderator_id = ?
    ");
    $stmt->execute([$project_id, $moderator_id]);

    $_SESSION['toast_success'] = "Moderator promoted to admin.";
    header("Location: /index.php?controller=ProjectUser&action=index&project_id=" . $project_id);
    exit;
}

public function demote()
{
    $project_id = $_POST['project_id'] ?? null;
    $moderator_id = $_POST['moderator_id'] ?? null;

    if (!$project_id || !$moderator_id) {
        echo "Missing data.";
        exit;
    }

    if (!$this->userIsProjectAdmin($project_id)) {
        echo "Access denied.";
        exit;
    }

    $stmt = $this->pdo->prepare("
        UPDATE project_user 
        SET is_admin = 0 
        WHERE project_id = ? AND moderator_id = ?
    ");
    $stmt->execute([$project_id, $moderator_id]);

    $_SESSION['toast_success'] = "Admin rights removed.";
    header("Location: /index.php?controller=ProjectUser&action=index&project_id=" . $project_id);
    exit;
}


public function cancelInvite()
{
    $project_id = $_GET['project_id'] ?? null;
    $invite_id = $_GET['invite_id'] ?? null;

    if (!$project_id || !$invite_id) {
        echo "Missing data.";
        exit;
    }

    if (!$this->userIsProjectAdmin($project_id)) {
        echo "Access denied.";
        exit;
    }

    // Delete the invite
    $stmt = $this->pdo->prepare("DELETE FROM project_invites WHERE id = ? AND project_id = ?");
    $stmt->execute([$invite_id, $project_id]);

    $_SESSION['toast_success'] = "Invite cancelled successfully!";
    header("Location: /index.php?controller=ProjectUser&action=index&project_id=" . $project_id);
    exit;
}

public function cancelEmailInvite()
{
    $project_id = $_GET['project_id'] ?? null;
    $email = $_GET['email'] ?? null;

    if (!$project_id || !$email) {
        $_SESSION['toast_error'] = "Missing project ID or email.";
        header('Location: /index.php?controller=ProjectUser&action=index&project_id=' . $project_id);
        exit;
    }

    if (!$this->userIsProjectAdmin($project_id)) {
        echo "Access denied.";
        exit;
    }

    $stmt = $this->pdo->prepare("
        DELETE FROM pending_invite_emails 
        WHERE project_id = ? AND email = ? AND status = 'sent'
    ");
    $stmt->execute([$project_id, $email]);

    $_SESSION['toast_success'] = "Email invite to $email canceled successfully.";
    header('Location: /index.php?controller=ProjectUser&action=index&project_id=' . $project_id);
    exit;
}


}
