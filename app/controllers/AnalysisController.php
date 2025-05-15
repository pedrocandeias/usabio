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

    // Get all test IDs in this project
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    $testIdList = $testIds ? implode(',', $testIds) : '0';

    // Get all participants for this project
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM participants WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $totalParticipants = $stmt->fetchColumn() ?? 0;

    // Main query: group by task text
    $stmt = $this->pdo->prepare("
        SELECT 
            r.question AS task_text,
            COUNT(*) AS total_responses,
            SUM(CASE WHEN r.evaluation_errors IS NULL OR r.evaluation_errors = '' THEN 1 ELSE 0 END) AS success_count,
            SUM(CASE WHEN r.evaluation_errors IS NOT NULL AND r.evaluation_errors != '' THEN 1 ELSE 0 END) AS fail_count,
            AVG(r.time_spent) AS avg_time
        FROM responses r
        JOIN evaluations e ON e.id = r.evaluation_id
        WHERE r.type = 'task' AND e.test_id IN ($testIdList)
        GROUP BY r.question
    ");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate median and stddev manually
    foreach ($tasks as &$task) {
        $stmt = $this->pdo->prepare("
            SELECT r.time_spent
            FROM responses r
            JOIN evaluations e ON e.id = r.evaluation_id
            WHERE r.type = 'task' 
              AND e.test_id IN ($testIdList) 
              AND r.question = ?
              AND r.time_spent > 0
            ORDER BY r.time_spent
        ");
        $stmt->execute([$task['task_text']]);
        $times = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'time_spent');

        $count = count($times);
        $task['median_time'] = $count ? ($count % 2 ? 
            $times[intval($count / 2)] : 
            ($times[$count / 2 - 1] + $times[$count / 2]) / 2) : 0;

        $mean = $task['avg_time'] ?? 0;
        $variance = 0;
        foreach ($times as $t) {
            $variance += pow($t - $mean, 2);
        }
        $task['stddev_time'] = $count ? round(sqrt($variance / $count), 2) : 0;

        // Success / fail / skipped rates
        $responses = $task['total_responses'] ?? 0;
        $task['success_rate'] = $responses ? round(($task['success_count'] / $responses) * 100, 1) : 0;
        $task['fail_rate'] = $responses ? round(($task['fail_count'] / $responses) * 100, 1) : 0;
        $task['skipped_rate'] = $totalParticipants ? round((($totalParticipants - $responses) / $totalParticipants) * 100, 1) : 0;
    }
    unset($task);

    // Sort by fail rate DESC
    usort($tasks, fn($a, $b) => $b['fail_rate'] <=> $a['fail_rate']);
    $taskStats = $tasks;
    // Breadcrumbs
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
        ['label' => 'Task Analysis', 'url' => '', 'active' => true],
    ];

    // View
    include __DIR__ . '/../views/analysis/tasks.php';
}

    // -------------------------------
    // Questionnaire-level Analysis
    public function questionnaires()
{
    $project_id = $_GET['id'] ?? 0;
    $this->checkProjectAccess($project_id);
    $project = $this->getProject($project_id);

    // Get all test IDs
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    $testIdList = $testIds ? implode(',', $testIds) : '0';

    // Get all questionnaire questions for this project
    $stmt = $this->pdo->prepare("
        SELECT q.id, q.text, q.question_type, q.question_options
        FROM questions q
        JOIN questionnaire_groups qg ON qg.id = q.questionnaire_group_id
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
          AND q.question_type IN ('radio', 'checkbox', 'dropdown')
    ");
    $stmt->execute([$project_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $questionStats = [];

    foreach ($questions as $q) {
        $questionId = $q['id'];
        $text = $q['text'];
        $type = $q['question_type'];
        $options = [];

        // Parse options
        foreach (explode(';', $q['question_options']) as $pair) {
            if (strpos($pair, ':') !== false) {
                [$label, $value] = explode(':', $pair, 2);
                $options[trim($value)] = trim($label);
            }
        }

        // Get answers for this question
        $stmt = $this->pdo->prepare("
            SELECT r.answer
            FROM responses r
            JOIN evaluations e ON e.id = r.evaluation_id
            WHERE r.type = 'question'
              AND e.test_id IN ($testIdList)
              AND r.question = ?
        ");
        $stmt->execute([$text]);
        $answers = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'answer');

        $counts = [];
        foreach ($answers as $ans) {
            if (is_string($ans) && strpos($ans, ',') !== false && $type === 'checkbox') {
                // multi-select answers (checkbox)
                foreach (explode(',', $ans) as $subAns) {
                    $subAns = trim($subAns);
                    $counts[$subAns] = ($counts[$subAns] ?? 0) + 1;
                }
            } else {
                $ans = trim($ans);
                $counts[$ans] = ($counts[$ans] ?? 0) + 1;
            }
        }

        // Calculate variance (to detect inconsistency)
        $total = array_sum($counts) ?: 1;
        $mean = $total / count($counts ?: [1]);
        $variance = 0;
        foreach ($counts as $val) {
            $variance += pow($val - $mean, 2);
        }
        $variance = count($counts) ? round($variance / count($counts), 2) : 0;

        $questionStats[] = [
            'text' => $text,
            'type' => $type,
            'options' => $options,
            'counts' => $counts,
            'variance' => $variance,
            'inconsistent' => $variance >= 5   // You can tune this threshold
        ];
    }

    $activeTab = 'questionnaires';
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
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
