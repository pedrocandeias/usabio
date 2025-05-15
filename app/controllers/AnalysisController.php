<?php
require_once __DIR__ . '/BaseController.php';

class AnalysisController extends BaseController
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

    private function checkProjectAccess($project_id)
    {
        if ($_SESSION['is_superadmin']) return;
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }
    }

    private function getProject($project_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            echo "Project not found.";
            exit;
        }
        return $project;
    }

    // -------------------------------
    // Main Overview
public function index()
{
    $project_id = $_GET['id'] ?? 0;
    $this->checkProjectAccess($project_id);
    $project = $this->getProject($project_id);

    // ✅ Get test IDs
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    $testIdList = $testIds ? implode(',', $testIds) : '0';

    // ✅ Global stats
    $totalEvaluations = $this->pdo->query("SELECT COUNT(*) FROM evaluations WHERE test_id IN ($testIdList)")->fetchColumn();
    $totalResponses = $this->pdo->query("SELECT COUNT(*) FROM responses WHERE evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
    $avgRaw = $this->pdo->query("SELECT AVG(time_spent) FROM responses WHERE time_spent > 0 AND evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
    $avgTime = $avgRaw !== null ? round($avgRaw) : 0;

    // ✅ Demographics
    $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total, AVG(participant_age) AS avg_age FROM participants WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $participantStats = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalParticipants = $participantStats['total'] ?? 0;
    $averageAge = $participantStats['avg_age'] ? round($participantStats['avg_age'], 1) : null;

    $stmt = $this->pdo->prepare("SELECT participant_gender, COUNT(*) AS count FROM participants WHERE project_id = ? GROUP BY participant_gender");
    $stmt->execute([$project_id]);
    $genderDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $this->pdo->prepare("SELECT participant_academic_level, COUNT(*) AS count FROM participants WHERE project_id = ? GROUP BY participant_academic_level");
    $stmt->execute([$project_id]);
    $educationDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Task Success Rate
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) FROM responses 
        WHERE type = 'task' AND evaluation_id IN (
            SELECT id FROM evaluations WHERE test_id IN ($testIdList)
        )
    ");
    $stmt->execute();
    $totalTasks = $stmt->fetchColumn() ?? 0;

    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) FROM responses 
        WHERE type = 'task' 
        AND (evaluation_errors IS NOT NULL AND evaluation_errors != '') 
        AND evaluation_id IN (
            SELECT id FROM evaluations WHERE test_id IN ($testIdList)
        )
    ");
    $stmt->execute();
    $totalErrors = $stmt->fetchColumn() ?? 0;

    $taskSuccessRate = $totalTasks > 0 
        ? round((($totalTasks - $totalErrors) / $totalTasks) * 100, 1) 
        : 0;

    // ✅ Breadcrumbs
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
        ['label' => 'Analysis Overview', 'url' => '', 'active' => true],
    ];

    // ✅ View
    include __DIR__ . '/../views/analysis/index.php';
}

    // -------------------------------
    // Task-level Analysis
    public function tasks()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        $activeTab = 'tasks';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
            ['label' => 'Task Analysis', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/tasks.php';
    }

    // -------------------------------
    // Questionnaire-level Analysis
    public function questionnaires()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        $activeTab = 'questionnaires';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
            ['label' => 'Questionnaire Analysis', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/questionnaires.php';
    }

    // -------------------------------
    // SUS scores Analysis
    public function sus()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        $activeTab = 'sus';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
            ['label' => 'SUS Analysis', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/sus.php';
    }

    // -------------------------------
    // Participant Analysis
    public function participants()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        $activeTab = 'participants';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
            ['label' => 'Participants', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/participants.php';
    }
}
