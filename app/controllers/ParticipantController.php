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
        $project_id = $_GET['project_id'] ?? 0;
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        // Access control
        if (!$_SESSION['is_admin'] && !$_SESSION['is_superadmin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Project info for breadcrumb
        $stmt = $this->pdo->prepare("SELECT title AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        // Load all participants from participants table
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Participants', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/participants/index.php';
    }

    public function show()
    {
        $participant_id = $_GET['id'] ?? 0;
        if (!$participant_id) {
            echo "Missing participant ID.";
            exit;
        }
    
        // Get participant data
        $participant = $this->participantModel->findInParticipants($participant_id);
        if (!$participant) {
            echo "Participant not found.";
            exit;
        }
    
        $project_id = $participant['project_id'];
    
        // Get project name for breadcrumb
        $stmt = $this->pdo->prepare("SELECT title AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Load any additional custom data for this participant
        $customData = $this->participantModel->customFields($participant_id);
    
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['project_name'] ?? 'Project', 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $project_id, 'active' => false],
            ['label' => $participant['participant_name'] ?? 'Participant', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/participants/show.php';
    }
    
    
    public function create()
    {
        $project_id = $_GET['project_id'] ?? 0;
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        // Access control
        if (!$_SESSION['is_admin'] && !$_SESSION['is_superadmin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
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
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        $customFields = [];
        if ($project_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
            $stmt->execute([$project_id]);
            $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $project_id, 'active' => false],
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

        $project_id = $_POST['project_id'] ?? 0;
        $testId = $_POST['test_id'] ?? 0;

        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        $name = trim($_POST['participant_name']);
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $level = $_POST['participant_academic_level'] ?? null;

        // Insert participant
        $stmt = $this->pdo->prepare(
            "
        INSERT INTO participants (
            project_id, test_id, participant_name, participant_age,
            participant_gender, participant_academic_level, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    "
        );
        $stmt->execute(
            [
            $project_id,
            $testId ?: null,
            $name,
            $age,
            $gender,
            $level
            ]
        );

        $participant_id = $this->pdo->lastInsertId();

        // ✅ Save assigned tests
        if (!empty($_POST['test_ids'])) {
            $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
            foreach ($_POST['test_ids'] as $testId) {
                $stmt->execute([$participant_id, $testId]);
            }
        }

        // ✅ Save custom participant data
        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participant_custom_data (participant_id, field_id, value)
            VALUES (?, ?, ?)
        "
            );
            foreach ($_POST['custom_field'] as $field_id => $value) {
                if ($value === '' || $value === null) { continue;
                }
                $stmt->execute([$participant_id, $field_id, $value]);
            }
        }

        header("Location: /index.php?controller=Participant&action=index&project_id=$project_id");
        exit;
    }

    public function edit()
    {
        $participant_id = $_GET['id'] ?? 0;
    
        if (!$participant_id) {
            echo "Missing participant ID.";
            exit;
        }
    
        // Fetch participant from database
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$participant) {
            echo "Participant not found.";
            exit;
        }
    
        // ✅ Use project_id from the participant itself
        $project_id = $participant['project_id'];
    
        // Fetch tests linked to this project
        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT test_id FROM participant_test WHERE participant_id = ?");
        $stmt->execute([$participant_id]);
        $assignedTestIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'test_id');
    
        // Fetch any custom fields for this project
        $customFields = [];
        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch saved custom field values
        $stmt = $this->pdo->prepare("
        SELECT field_id, value
        FROM participant_custom_data
        WHERE participant_id = ?
        ");
        $stmt->execute([$participant_id]);

        $customFieldValues = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $customFieldValues[$row['field_id']] = $row['value'];
        }

    

        
        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => 'Participants', 'url' => '/index.php?controller=Participant&action=index&project_id=' . $project_id, 'active' => false],
            ['label' => 'Edit Participant', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/participants/form.php';
    }
    

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }

        $participant_id = $_POST['participant_id'] ?? null;
        $project_id = $_POST['project_id'] ?? null;
        $testId = $_POST['test_id'] ?? null;

        if (!$participant_id || !$project_id) {
            echo "Missing participant or project ID.";
            exit;
        }

        $name = trim($_POST['participant_name']);
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $level = $_POST['participant_academic_level'] ?? null;

        // Optional: Check project ID validity
        $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM projects WHERE id = ?");
        $checkStmt->execute([$project_id]);
        if ($checkStmt->fetchColumn() == 0) {
            echo "Invalid project ID: $project_id";
            exit;
        }

        // Update test assignments
        $stmt = $this->pdo->prepare("DELETE FROM participant_test WHERE participant_id = ?");
        $stmt->execute([$participant_id]);

        if (!empty($_POST['test_ids'])) {
            $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
            foreach ($_POST['test_ids'] as $testId) {
                $stmt->execute([$participant_id, $testId]);
            }
        }

        // Update core participant fields
        $stmt = $this->pdo->prepare(
            "
        UPDATE participants SET
            participant_name = ?,
            participant_age = ?,
            participant_gender = ?,
            participant_academic_level = ?,
            project_id = ?,
            test_id = ?,
            updated_at = NOW()
        WHERE id = ?
    "
        );

        $stmt->execute(
            [
            $name,
            $age,
            $gender,
            $level,
            $project_id,
            $testId ?: null,
            $participant_id
            ]
        );

        // ✅ Update participant_custom_data
        $stmt = $this->pdo->prepare("DELETE FROM participant_custom_data WHERE participant_id = ?");
        $stmt->execute([$participant_id]);

        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participant_custom_data (participant_id, field_id, value)
            VALUES (?, ?, ?)
        "
            );
            foreach ($_POST['custom_field'] as $field_id => $value) {
                if ($value === '' || $value === null) { continue;
                }
                $stmt->execute([$participant_id, $field_id, $value]);
            }
        }

        header("Location: /index.php?controller=Participant&action=edit&id=$participant_id&saved=1");
        exit;
        
    }

    public function destroy()
    {
        $participant_id = $_GET['id'] ?? 0;
        $project_id = $_GET['project_id'] ?? 0;

        if (!$participant_id || !$project_id) {
            echo "Missing parameters.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);

        header("Location: /index.php?controller=Participant&action=index&project_id=$project_id");
        exit;
    }
}
