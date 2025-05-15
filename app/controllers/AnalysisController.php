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

    // Main overview

    public function index()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        // ✅ Fetch project
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // ✅ Fetch test IDs
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = count($testIds) > 0 ? implode(',', $testIds) : '0';

        // ✅ Global stats
        $totalEvaluations = $this->pdo->query("SELECT COUNT(*) FROM evaluations WHERE test_id IN ($testIdList)")->fetchColumn();
        $totalResponses = $this->pdo->query("SELECT COUNT(*) FROM responses WHERE evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgRaw = $this->pdo->query("SELECT AVG(time_spent) FROM responses WHERE time_spent > 0 AND evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgTime = $avgRaw !== null ? round($avgRaw) : 0;
        
        // ✅ SUS Analysis
        $susScores = [];
        $susBreakdown = [];
        $susChartLabels = [];
        $susChartScores = [];

        // Get all questionnaire groups for this project
        $stmt = $this->pdo->prepare("SELECT qg.id AS group_id FROM questionnaire_groups qg JOIN tests t ON t.id = qg.test_id WHERE t.project_id = ?");
        $stmt->execute([$project_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groups as $group) {
            $groupId = $group['group_id'];

            // Fetch SUS questions
            $stmt = $this->pdo->prepare("SELECT id, text FROM questions WHERE questionnaire_group_id = ? AND preset_type = 'SUS' ORDER BY position ASC");
            $stmt->execute([$groupId]);
            $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($susQuestions) !== 10) {
                continue;
            }

            // Get all responses for this project
            $stmt = $this->pdo->prepare("SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer FROM evaluations e JOIN responses r ON r.evaluation_id = e.id WHERE e.test_id IN (SELECT id FROM tests WHERE project_id = ?)");
            $stmt->execute([$project_id]);
            $allResponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by evaluation
            $byEval = [];
            foreach ($allResponses as $resp) {
                $byEval[$resp['evaluation_id']]['participant'] = $resp['participant_name'] ?: 'Anonymous';
                $byEval[$resp['evaluation_id']]['answers'][$resp['question']] = (int) $resp['answer'];
            }

            foreach ($byEval as $evalId => $entry) {
                $participant = $entry['participant'];
                $answers = $entry['answers'];

                $score = 0;
                $valid = true;
                $individualAnswers = [];

                foreach ($susQuestions as $i => $q) {
                    $qText = $q['text'];
                    $answer = $answers[$qText] ?? null;

                    if ($answer === null || $answer < 1 || $answer > 5) {
                        $valid = false;
                        break;
                    }

                    $individualAnswers[] = $answer;
                    $score += ($i % 2 === 0) ? ($answer - 1) : (5 - $answer);
                }

                if ($valid) {
                    $sus = $score * 2.5;
                    $susScores[] = ['participant' => $participant, 'score' => $sus];
                    $susBreakdown[] = [
                        'participant' => $participant,
                        'answers' => $individualAnswers,
                        'score' => $sus,
                        'label' => $sus >= 85 ? 'Excellent' : ($sus >= 70 ? 'Good' : ($sus >= 50 ? 'OK' : 'Poor'))
                    ];
                    $susChartLabels[] = $participant;
                    $susChartScores[] = $sus;
                }
            }
        }

        // ✅ SUS Summary
        $susSummary = null;
        if (!empty($susBreakdown)) {
            $scores = array_column($susBreakdown, 'score');
            $avg = round(array_sum($scores) / count($scores), 1);
            $min = min($scores);
            $max = max($scores);
            $variation = ($max - $min) >= 30 ? 'high' : (($max - $min) >= 15 ? 'moderate' : 'low');
            $label = $avg >= 85 ? 'Excellent' : ($avg >= 70 ? 'Good' : ($avg >= 50 ? 'OK' : 'Poor'));
            $lowScores = count(array_filter($scores, fn($s) => $s < 50));
            $susSummary = [
                'average' => $avg,
                'label' => $label,
                'low' => $lowScores,
                'variation' => $variation
            ];
        }

        // ✅ Task analysis
        $stmt = $this->pdo->prepare("SELECT r.question AS task_text, COUNT(*) AS total_responses, AVG(r.time_spent) AS avg_time, SUM(CASE WHEN r.evaluation_errors IS NOT NULL AND r.evaluation_errors != '' THEN 1 ELSE 0 END) AS error_count FROM responses r JOIN evaluations e ON e.id = r.evaluation_id WHERE r.type = 'task' AND e.test_id IN ($testIdList) GROUP BY r.question ORDER BY total_responses DESC");
        $stmt->execute();
        $taskStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($taskStats as &$t) {
            $errors = $t['error_count'] ?? 0;
            $total = $t['total_responses'] ?? 1;
            $t['success_rate'] = round((($total - $errors) / $total) * 100, 1);
        }
        unset($t);

        // ✅ Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=edit&id=' . $project_id, 'active' => false],
            ['label' => 'Analysis', 'url' => '', 'active' => true],
        ];

        // ✅ View
        include __DIR__ . '/../views/analysis/index.php';
    }

    // Tasks analysis
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
    // Questionnaire analysis
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
    // SUS scores analysis
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
    // Participants analysis
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

    // -------------------------------
    // Optional: Export (future)
    public function export()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        $activeTab = 'export';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id='.$project_id, 'active' => false],
            ['label' => 'Export', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/export.php';
    }
}
