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
    
        // Fetch project info for breadcrumbs
        $stmt = $this->pdo->prepare("SELECT product_under_test AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Load all participants for the project
        $stmt = $this->pdo->prepare("SELECT * FROM evaluations WHERE test_id IN (SELECT id FROM tests WHERE project_id = ?) ORDER BY timestamp DESC");
        $stmt->execute([$projectId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($participants as &$p) {
            $p['participant_id'] = $p['id'];
            $p['participant_name'] = htmlspecialchars($p['participant_name']);
            $p['participant_age'] = htmlspecialchars($p['participant_age']);
            $p['participant_gender'] = htmlspecialchars($p['participant_gender']);		
            $p['participant_academic_level'] = htmlspecialchars($p['participant_academic_level']);
            $p['participant_name'] = urlencode($p['participant_name']);


            $p['participant_name_encoded'] = urlencode($p['participant_name']);
        }

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

        $participant = $this->participantModel->find($id);
        if (!$participant) {
            echo "Participant not found.";
            exit;
        }

        include __DIR__ . '/../views/participants/show.php';
    }

    // You can later add exportCsv, edit, update, etc.
}
