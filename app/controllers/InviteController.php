<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProjectInvite.php';

class InviteController extends BaseController
{
    protected $inviteModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->inviteModel = new ProjectInvite($pdo);
    }

    public function create()
{
    $project_id = $_POST['project_id'] ?? null;
    $email = trim($_POST['email'] ?? '');

    if (!$project_id || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&error=invalid_email');
        exit;
    }

    // Carrega info do projeto (para incluir no email)
    $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        header('Location: /?controller=Project&action=index&error=project_not_found');
        exit;
    }

    require_once __DIR__ . '/../models/ProjectInvite.php';
    $inviteModel = new ProjectInvite($this->pdo);

    // Verifica se o utilizador já existe
    $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE email = ?");
    $stmt->execute([$email]);
    $moderator_id = $stmt->fetchColumn();

    if ($moderator_id) {
        // Já existe — verificar se já foi convidado
        if ($inviteModel->inviteExists($project_id, $moderator_id)) {
            header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&error=already_invited');
            exit;
        }

        // Criar convite real
        $inviteModel->createInvite($project_id, $moderator_id);

        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&success=invite_sent');
        exit;
    } else {
        // Verificar se já foi convidado por email
        $stmt = $this->pdo->prepare("
            SELECT id FROM pending_invite_emails 
            WHERE project_id = ? AND email = ?
        ");
        $stmt->execute([$project_id, $email]);
        $alreadyPending = $stmt->fetchColumn();

        if (!$alreadyPending) {
            // Registar convite pendente
            $stmt = $this->pdo->prepare("
                INSERT INTO pending_invite_emails (project_id, email, status, created_at)
                VALUES (?, ?, 'sent', NOW())
            ");
            $stmt->execute([$project_id, $email]);

            // Enviar email com PHPMailer
            require_once __DIR__ . '/../helpers/MailHelper.php';
            $registerLink = "https://usabio.ddev.site/index.php?controller=Auth&action=register&prefill=" . urlencode($email);
            MailHelper::sendInviteEmail($email, $project['title'], $registerLink);
        }

        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&success=invite_email_sent');
        exit;
    }
}


    public function index()
    {
        $moderator_id = $_SESSION['user_id'];
        $invites = $this->inviteModel->getPendingInvitesForModerator($moderator_id);


        $breadcrumbs = [
            ['label' => 'User Settings', 'url' => '', 'active' => false],
            ['label' => 'Project Invites', 'url' => '', 'active' => true],
        ];
        include __DIR__ . '/../views/invites/index.php';
    }

    public function respond()
    {
        $invite_id = $_POST['invite_id'] ?? null;
        $action = $_POST['action'] ?? null;

        if (!$invite_id || !in_array($action, ['accepted', 'declined'])) {
            header('Location: /?controller=Invite&action=index&error=invalid_request');
            exit;
        }

        $invite = $this->inviteModel->find($invite_id);

        if (!$invite || $invite['moderator_id'] != $_SESSION['user_id']) {
            header('Location: /?controller=Invite&action=index&error=not_authorized');
            exit;
        }

        $this->inviteModel->updateInviteStatus($invite_id, $action);

        if ($action === 'accepted') {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            $stmt->execute([$invite['project_id'], $_SESSION['user_id']]);
        }

        header('Location: /?controller=Invite&action=index&success=' . $action);
        exit;
    }

    public function cancel()
{
    $project_id = $_POST['project_id'] ?? null;
    $moderator_id = $_POST['moderator_id'] ?? null;

    if (!$project_id || !$moderator_id) {
        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&error=missing_data');
        exit;
    }

    $stmt = $this->pdo->prepare("DELETE FROM project_invites WHERE project_id = ? AND moderator_id = ? AND status = 'pending'");
    $stmt->execute([$project_id, $moderator_id]);

    header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&success=invite_cancelled');
    exit;
}

public function cancelEmail()
{
    $project_id = $_POST['project_id'] ?? null;
    $email = $_POST['email'] ?? null;

    if (!$project_id || !$email) {
        header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&error=missing_data');
        exit;
    }

    $stmt = $this->pdo->prepare("DELETE FROM pending_invite_emails WHERE project_id = ? AND email = ?");
    $stmt->execute([$project_id, $email]);

    header('Location: /?controller=ProjectUser&action=index&project_id=' . $project_id . '&success=email_invite_cancelled');
    exit;
}



}
