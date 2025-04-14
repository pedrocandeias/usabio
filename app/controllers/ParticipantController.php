<?php

require_once __DIR__ . '/../models/Participant.php';

class ParticipantController
{
    private $pdo;
    private $participantModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->participantModel = new Participant($pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /?controller=Auth&action=login');
            exit;
        }
    }

    public function index()
    {
        $projectId = $_GET['project_id'] ?? 0;
        if (!$projectId) {
            echo "Missing project ID.";
            exit;
        }

        // Access control
        if (!$_SESSION['is_admin'] && !$_SESSION['is_superadmin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Project info for breadcrumb
        $stmt = $this->pdo->prepare("SELECT title AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        // Load all participants from participants table
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$projectId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $projectId, 'active' => false],
            ['label' => 'Participants', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/participants/index.php';
    }

    public function show()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo "Missing participant ID.";
            exit;
        }
    
        // Get participant data
        $participant = $this->participantModel->findInParticipants($id);
        if (!$participant) {
            echo "Participant not found.";
            exit;
        }
    
        $projectId = $participant['project_id'];
    
        // Get project name for breadcrumb
        $stmt = $this->pdo->prepare("SELECT title AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Load any additional custom data for this participant
        $customData = $this->participantModel->customFields($id);
    
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['project_name'] ?? 'Project', 'url' => '/index.php?controller=Project&action=show&id=' . $projectId, 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $projectId, 'active' => false],
            ['label' => $participant['participant_name'] ?? 'Participant', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/participants/show.php';
    }
    
    
    public function create()
    {
        $projectId = $_GET['project_id'] ?? 0;
        if (!$projectId) {
            echo "Missing project ID.";
            exit;
        }

        // Access control
        if (!$_SESSION['is_admin'] && !$_SESSION['is_superadmin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        $participant = [
            'id' => 0,
            'participant_name' => '',
            'participant_age' => '',
            'participant_gender' => '',
            'participant_academic_level' => '',
        ];

        // Get tests for this project to help pre-select a test
        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        $customFields = [];
        if ($projectId) {
            $stmt = $this->pdo->prepare("SELECT * FROM project_custom_fields WHERE project_id = ? ORDER BY position ASC");
            $stmt->execute([$projectId]);
            $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $projectId, 'active' => false],
            ['label' => 'Add Participant', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/participants/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }

        $projectId = $_POST['project_id'] ?? 0;
        $testId = $_POST['test_id'] ?? 0;

        if (!$projectId) {
            echo "Missing project ID.";
            exit;
        }

        $name = trim($_POST['participant_name']);
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $level = $_POST['participant_academic_level'] ?? null;

        $stmt = $this->pdo->prepare("
            INSERT INTO participants (
                project_id, test_id, participant_name, participant_age,
                participant_gender, participant_academic_level, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $projectId,
            $testId ?: null,
            $name,
            $age,
            $gender,
            $level
        ]);

        // TODO: Also support saving into participant_custom_data if you extend it

        header("Location: /index.php?controller=Participant&action=index&project_id=$projectId");
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;

        if (!$id || !$projectId) {
            echo "Missing parameters.";
            exit;
        }

        // Fetch participant from participants table
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {
            echo "Participant not found.";
            exit;
        }

        // Fetch tests
        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $customFields = [];

        if ($projectId) {
            $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
            $stmt->execute([$projectId]);
            $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $projectId, 'active' => false],
            ['label' => 'Edit Participant', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/participants/form.php';
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;
        $projectId = $_GET['project_id'] ?? 0;

        if (!$id || !$projectId) {
            echo "Missing parameters.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM participants WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Participant&action=index&project_id=$projectId");
        exit;
    }
}
