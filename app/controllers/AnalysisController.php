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

    public function getProject($project_id)
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

        // Get test IDs
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = $testIds ? implode(',', $testIds) : '0';

        // Global stats
        $totalEvaluations = $this->pdo->query("SELECT COUNT(*) FROM evaluations WHERE test_id IN ($testIdList)")->fetchColumn();
        $totalResponses = $this->pdo->query("SELECT COUNT(*) FROM responses WHERE evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgRaw = $this->pdo->query("SELECT AVG(time_spent) FROM responses WHERE time_spent > 0 AND evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgTime = $avgRaw !== null ? round($avgRaw) : 0;

        // Demographics
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

        // Task Success Rate
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

        // SUS Summary (same logic as before)
        $susSummary = null;
        $stmt = $this->pdo->prepare("
        SELECT qg.id AS group_id
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
    ");
        $stmt->execute([$project_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $susScores = [];
        foreach ($groups as $group) {
            $groupId = $group['group_id'];

            $stmt = $this->pdo->prepare("
            SELECT id, text 
            FROM questions
            WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
            ORDER BY position ASC
        ");
            $stmt->execute([$groupId]);
            $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($susQuestions) !== 10) continue;

            $stmt = $this->pdo->prepare("
            SELECT e.id AS evaluation_id, r.question, r.answer
            FROM evaluations e
            JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN ($testIdList)
            AND r.question IN (" . implode(',', array_fill(0, count($susQuestions), '?')) . ")
        ");
            $questionTexts = array_column($susQuestions, 'text');
            $stmt->execute($questionTexts);
            $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $byEval = [];
            foreach ($responses as $r) {
                $byEval[$r['evaluation_id']]['answers'][$r['question']] = (int)$r['answer'];
            }

            foreach ($byEval as $entry) {
                if (count($entry['answers']) !== 10) continue;

                $score = 0;
                foreach ($susQuestions as $i => $q) {
                    $a = $entry['answers'][$q['text']] ?? null;
                    if ($a === null || $a < 1 || $a > 5) continue 2;
                    $score += ($i % 2 === 0) ? ($a - 1) : (5 - $a);
                }
                $susScores[] = $score * 2.5;
            }
        }

        if (!empty($susScores)) {
            $avg = round(array_sum($susScores) / count($susScores), 1);
            $min = min($susScores);
            $max = max($susScores);
            $variation = ($max - $min) >= 30 ? 'high' : (($max - $min) >= 15 ? 'moderate' : 'low');
            $label = $avg >= 85 ? 'Excellent' : ($avg >= 70 ? 'Good' : ($avg >= 50 ? 'OK' : 'Poor'));
            $lowScores = count(array_filter($susScores, fn($s) => $s < 50));
            $susSummary = [
                'average' => $avg,
                'label' => $label,
                'low' => $lowScores,
                'variation' => $variation
            ];
        }

        // Load latest saved AI summary (manually generated)
        $stmt = $this->pdo->prepare("
        SELECT ai_summary, last_updated 
        FROM usability_results 
        WHERE project_id = ? 
        ORDER BY last_updated DESC 
        LIMIT 1
    ");
        $stmt->execute([$project_id]);
        $aiSummary = $stmt->fetch(PDO::FETCH_ASSOC);


        // Task-level stats
        $taskStats = [];
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

        // Calcula median, stddev e rates
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
                $times[intval($count / 2)] : ($times[$count / 2 - 1] + $times[$count / 2]) / 2) : 0;

            $mean = $task['avg_time'] ?? 0;
            $variance = 0;
            foreach ($times as $t) {
                $variance += pow($t - $mean, 2);
            }
            $task['stddev_time'] = $count ? round(sqrt($variance / $count), 2) : 0;

            $responses = $task['total_responses'] ?? 0;
            $task['success_rate'] = $responses ? round(($task['success_count'] / $responses) * 100, 1) : 0;
            $task['fail_rate'] = $responses ? round(($task['fail_count'] / $responses) * 100, 1) : 0;
        }
        unset($task);
        $taskStats = $tasks;

        $problematicTasks = [];
        $failRateThreshold = 30;   // percentagem de falha considerada alta
        $stddevThreshold   = 20;   // desvio padrão elevado (ajusta conforme desejado)
        $lowSuccessThresh  = 60;   // percentagem de sucesso considerada baixa

        foreach ($taskStats as $task) {
            $isProblem = false;
            $reasons = [];

            if ($task['fail_rate'] > $failRateThreshold) {
                $isProblem = true;
                $reasons[] = "High fail rate ({$task['fail_rate']}%)";
            }
            if ($task['success_rate'] < $lowSuccessThresh) {
                $isProblem = true;
                $reasons[] = "Low success rate ({$task['success_rate']}%)";
            }
            if ($task['stddev_time'] > $stddevThreshold) {
                $isProblem = true;
                $reasons[] = "High time variability (Std Dev {$task['stddev_time']})";
            }
            if ($isProblem) {
                $problematicTasks[] = [
                    'task_text' => $task['task_text'],
                    'fail_rate' => $task['fail_rate'],
                    'success_rate' => $task['success_rate'],
                    'stddev_time' => $task['stddev_time'],
                    'reasons' => $reasons
                ];
            }
        }



        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Analysis Overview', 'url' => '', 'active' => true],
        ];

        // View
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
                $times[intval($count / 2)] : ($times[$count / 2 - 1] + $times[$count / 2]) / 2) : 0;

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

        $chartData = [
            'labels' => array_column($taskStats, 'task_text'),
            'successRates' => array_column($taskStats, 'success_rate'),
            'failRates' => array_column($taskStats, 'fail_rate'),
            'skippedRates' => array_column($taskStats, 'skipped_rate'),
            'medianTimes' => array_column($taskStats, 'median_time'),
        ];

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

        if (empty($testIds)) {
            $testIds = [0]; // prevent empty IN clause
        }

        $placeholders = implode(',', array_fill(0, count($testIds), '?'));

        // Get all questionnaire questions for this project
        $stmt = $this->pdo->prepare("
        SELECT q.id, q.text, q.question_type, q.question_options, q.preset_type
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

            foreach (explode(';', $q['question_options']) as $pair) {
                if (strpos($pair, ':') !== false) {
                    [$label, $value] = explode(':', $pair, 2);
                    $options[trim($value)] = trim($label);
                }
            }

            $sql = "
        SELECT r.answer
        FROM responses r
        JOIN evaluations e ON e.id = r.evaluation_id
        WHERE r.type IN ('question', 'questionnaire')
          AND e.test_id IN ($placeholders)
          AND r.question_id = ?
    ";
            $params = array_merge($testIds, [$questionId]);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $answers = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'answer');

            $counts = [];
            foreach ($answers as $ans) {
                if (is_string($ans) && strpos($ans, ',') !== false && $type === 'checkbox') {
                    foreach (explode(',', $ans) as $subAns) {
                        $subAns = trim($subAns);
                        if ($subAns !== '') {
                            $counts[$subAns] = ($counts[$subAns] ?? 0) + 1;
                        }
                    }
                } else {
                    $ans = trim($ans);
                    if ($ans !== '') {
                        $counts[$ans] = ($counts[$ans] ?? 0) + 1;
                    }
                }
            }

            $responseCount = array_sum($counts);
            $uniqueOptions = count($counts);
            $mean = $uniqueOptions > 0 ? ($responseCount / $uniqueOptions) : 0;
            $variance = 0;
            if ($uniqueOptions > 0) {
                foreach ($counts as $val) {
                    $variance += pow($val - $mean, 2);
                }
                $variance = round($variance / $uniqueOptions, 2);
            }

            $questionStats[] = [
                'text' => $text,
                'type' => $type,
                'options' => $options,
                'counts' => $counts,
                'variance' => $variance,
                'inconsistent' => $variance >= 5,
                'is_sus' => ($q['preset_type'] ?? '') === 'SUS'
            ];
        }

        // Prepara dados para os gráficos (confirmar que tens isto)
        $chartData = [];

        foreach ($questionStats as $q) {
            $chartData[] = [
                'question' => $q['text'],
                'labels' => array_values($q['options']),
                'counts' => array_values(array_map(
                    fn($value) => $q['counts'][$value] ?? 0,
                    array_keys($q['options'])
                )),
                'is_sus' => $q['is_sus']
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

    public function sus()
    {
        $debug = true;   // Set to false to disable debug output

        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);
        $project = $this->getProject($project_id);

        // Get test IDs
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = $testIds ? implode(',', $testIds) : '0';

        if ($debug) {
            echo "<pre>DEBUG: project_id = $project_id\n";
            echo "Test IDs: " . implode(',', $testIds) . "\n";
        }

        // Find questionnaire groups with exactly 10 SUS questions
        $stmt = $this->pdo->prepare("
        SELECT qg.id AS group_id
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
    ");
        $stmt->execute([$project_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $susScores = [];
        $susBreakdown = [];
        $susChartLabels = [];
        $susChartScores = [];

        if ($debug) {
            echo "Found questionnaire groups: ";
            echo implode(',', array_column($groups, 'group_id')) . "\n";
        }

        foreach ($groups as $group) {
            $groupId = $group['group_id'];

            $stmt = $this->pdo->prepare("
            SELECT id, text 
            FROM questions
            WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
            ORDER BY position ASC
        ");
            $stmt->execute([$groupId]);
            $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($susQuestions) !== 10) {
                continue;
            }

            if ($debug) {
                echo "Group $groupId has 10 SUS questions. Proceeding...\n";
            }

            // Get all responses
            $stmt = $this->pdo->prepare("
            SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer
            FROM evaluations e
            JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN ($testIdList)
        ");
            $stmt->execute();
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

        if ($debug) {
            echo "Total valid SUS evaluations: " . count($susBreakdown) . "\n";
            echo "</pre>";
        }

        $activeTab = 'sus';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
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

        // Get all test IDs
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = $testIds ? implode(',', $testIds) : '0';

        // Load participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get custom fields
        $stmt = $this->pdo->prepare("SELECT id, label FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map custom field values
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $placeholders = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare("SELECT participant_id, field_id, value FROM participant_custom_data WHERE participant_id IN ($placeholders)");
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // Get all SUS question texts (preset_type = 'SUS')
        $stmt = $this->pdo->prepare("
        SELECT text
        FROM questions
        WHERE questionnaire_group_id IN (
            SELECT id FROM questionnaire_groups WHERE test_id IN ($testIdList)
        ) AND preset_type = 'SUS'
    ");
        $stmt->execute();
        $susQuestions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'text');

        $results = [];
        foreach ($participants as $p) {
            $participantId = $p['id'];

            // Get evaluations for this participant (tasks + questionnaire)
            $stmt = $this->pdo->prepare("
            SELECT id, did_tasks, did_questionnaire
            FROM evaluations
            WHERE test_id IN ($testIdList) AND participant_name = ?
        ");
            $stmt->execute([$p['participant_name']]);
            $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate task success
            $totalTasks = 0;
            $successTasks = 0;
            foreach ($evaluations as $eval) {
                $stmt = $this->pdo->prepare("
                SELECT evaluation_errors
                FROM responses
                WHERE evaluation_id = ? AND type = 'task'
            ");
                $stmt->execute([$eval['id']]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $totalTasks += count($tasks);
                foreach ($tasks as $task) {
                    if (empty($task['evaluation_errors'])) {
                        $successTasks++;
                    }
                }
            }
            $taskSuccessRate = $totalTasks > 0 ? round(($successTasks / $totalTasks) * 100, 1) : 0;

            // SUS score
            $susScore = null;
            if (!empty($susQuestions)) {
                $stmt = $this->pdo->prepare("
                SELECT r.answer, r.question
                FROM responses r
                JOIN evaluations e ON e.id = r.evaluation_id
                WHERE e.test_id IN ($testIdList) 
                AND e.participant_name = ?
                AND r.type IN ('question', 'questionnaire')
                AND r.question IN (" . implode(',', array_fill(0, count($susQuestions), '?')) . ")
                AND r.answer BETWEEN 1 AND 5
            ");
                $params = array_merge([$p['participant_name']], $susQuestions);
                $stmt->execute($params);
                $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $answers = array_column($responses, 'answer');
                if (count($answers) === 10) {
                    $score = 0;
                    foreach ($answers as $i => $answer) {
                        $score += ($i % 2 === 0) ? ($answer - 1) : (5 - $answer);
                    }
                    $susScore = round($score * 2.5, 1);
                }
            }

            // Add participant
            $participantData = [
                'participant_name' => $p['participant_name'] ?? 'Anonymous',
                'participant_age' => $p['participant_age'],
                'participant_gender' => $p['participant_gender'],
                'participant_academic_level' => $p['participant_academic_level'],
                'tasks_completed' => $totalTasks,
                'task_success' => $taskSuccessRate,
                'questionnaire_completed' => !empty(array_filter(array_column($evaluations, 'did_questionnaire'))),
                'sus_score' => $susScore,
                'custom_fields' => []
            ];

            foreach ($customFields as $field) {
                $participantData['custom_fields'][$field['label']] = $customData[$participantId][$field['id']] ?? null;
            }

            $results[] = $participantData;
        }

        $participants = $results;

        // Correlation calculation
        $correlationData = [
            'gender' => [],
            'academic_level' => [],
            'age_group' => []
        ];

        foreach ($participants as $p) {
            // gender
            $g = $p['participant_gender'] ?? 'Unknown';
            $correlationData['gender'][$g][] = $p;
            // academic level
            $al = $p['participant_academic_level'] ?? 'Unknown';
            $correlationData['academic_level'][$al][] = $p;
            // age group
            $age = intval($p['participant_age']);
            $group = ($age < 18) ? '<18' : (($age <= 24) ? '18-24' : (($age <= 34) ? '25-34' : (($age <= 44) ? '35-44' : (($age <= 54) ? '45-54' : (($age <= 64) ? '55-64' : '65+')))));
            $correlationData['age_group'][$group][] = $p;
        }

        // Custom field correlation
        foreach ($participants as $p) {
            foreach ($p['custom_fields'] as $fieldLabel => $value) {
                if (!empty($value)) {
                    $correlationData['custom_field_' . $fieldLabel][$value][] = $p;
                }
            }
        }

        // Calculate averages
        foreach ($correlationData as $groupType => &$groups) {
            foreach ($groups as $value => $groupParticipants) {
                $count = count($groupParticipants);
                $taskSuccessSum = array_sum(array_column($groupParticipants, 'task_success'));
                $susValues = array_column($groupParticipants, 'sus_score');
                $susValid = array_filter($susValues, fn($v) => $v !== null);
                $groups[$value] = [
                    'count' => $count,
                    'avg_task_success' => $count ? round($taskSuccessSum / $count, 1) : 0,
                    'avg_sus' => $susValid ? round(array_sum($susValid) / count($susValid), 1) : null
                ];
            }
        }
        unset($groups);

        $correlationChartData = [];
        foreach ($correlationData as $groupType => $groups) {
            $correlationChartData[$groupType] = [
                'labels' => array_keys($groups),
                'task_success' => array_column($groups, 'avg_task_success'),
                'sus_score' => array_map(function ($g) {
                    return $g['avg_sus'] ?? null;
                }, $groups),
                'count' => array_column($groups, 'count'),
            ];
        }


        $activeTab = 'participants';
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Participants Analysis', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/analysis/participants.php';
    }



    public function generateSummaryNow()
    {
        $project_id = $_GET['id'] ?? 0;
        $this->checkProjectAccess($project_id);

        // 1. Coletar dados relevantes do projeto
        $summaryData = $this->collectAnalysisData($project_id);

        // 2. Gerar sumário via AI
        require_once __DIR__ . '/../helpers/openai.php';
        $aiSummary = generateAISummary($this->pdo, $summaryData);

        // 3. Gravar na base de dados (tabela usability_results)
        $stmt = $this->pdo->prepare("
        INSERT INTO usability_results (project_id, ai_summary, last_updated)
        VALUES (?, ?, NOW())
    ");
        $stmt->execute([$project_id, $aiSummary]);

        // 4. Redirecionar de volta para index() (podes adaptar para AJAX se necessário)
        $_SESSION['toast_success'] = "AI usability summary generated successfully!";
        header("Location: /index.php?controller=Analysis&action=index&id=" . $project_id);
        exit;
    }




    protected function collectAnalysisData($project_id)
    {
        // Exemplo baseado no teu index()
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = $testIds ? implode(',', $testIds) : '0';

        // Resto dos dados, igual ao que fazes em index()
        $totalEvaluations = $this->pdo->query("SELECT COUNT(*) FROM evaluations WHERE test_id IN ($testIdList)")->fetchColumn();
        $totalResponses = $this->pdo->query("SELECT COUNT(*) FROM responses WHERE evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgRaw = $this->pdo->query("SELECT AVG(time_spent) FROM responses WHERE time_spent > 0 AND evaluation_id IN (SELECT id FROM evaluations WHERE test_id IN ($testIdList))")->fetchColumn();
        $avgTime = $avgRaw !== null ? round($avgRaw) : 0;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total, AVG(participant_age) AS avg_age FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $participantStats = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalParticipants = $participantStats['total'] ?? 0;
        $averageAge = $participantStats['avg_age'] ? round($participantStats['avg_age'], 1) : null;

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

        // SUS summary: copia o teu cálculo atual
        $susSummary = null;
        // ...[calcula como em index()]

        $stmt = $this->pdo->prepare("SELECT title FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $projectTitle = $stmt->fetchColumn();

        return [
            'project' => $projectTitle,
            'sus' => $susSummary,
            'taskSuccessRate' => $taskSuccessRate,
            'totalEvaluations' => $totalEvaluations,
            'totalResponses' => $totalResponses,
            'avgTime' => $avgTime,
            'participantCount' => $totalParticipants,
            'averageAge' => $averageAge,
        ];
    }

    public function getTaskStats($project_id)
    {
        // 1. Get test IDs for this project
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $testIdList = $testIds ? implode(',', $testIds) : '0';

        // 2. Get total number of participants
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $totalParticipants = $stmt->fetchColumn() ?? 0;

        // 3. Get main task stats grouped by task_text (question)
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

        // 4. Calculate median, stddev, and rates for each task
        foreach ($tasks as &$task) {
            // Get all time_spent values for median/stddev
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

            // Median
            $count = count($times);
            if ($count > 0) {
                sort($times);
                $mid = (int)($count / 2);
                $task['median_time'] = $count % 2 ? $times[$mid] : (($times[$mid - 1] + $times[$mid]) / 2);
            } else {
                $task['median_time'] = 0;
            }

            // Stddev
            $mean = $task['avg_time'] ?? 0;
            $variance = 0;
            foreach ($times as $t) {
                $variance += pow($t - $mean, 2);
            }
            $task['stddev_time'] = $count ? round(sqrt($variance / $count), 2) : 0;

            // Success, fail, skipped rates
            $responses = $task['total_responses'] ?? 0;
            $task['success_rate'] = $responses ? round(($task['success_count'] / $responses) * 100, 1) : 0;
            $task['fail_rate'] = $responses ? round(($task['fail_count'] / $responses) * 100, 1) : 0;
            $task['skipped_rate'] = $totalParticipants ? round((($totalParticipants - $responses) / $totalParticipants) * 100, 1) : 0;
        }
        unset($task);

        // 5. Sort by fail rate DESC (optional, like your view)
        usort($tasks, fn($a, $b) => $b['fail_rate'] <=> $a['fail_rate']);

        return $tasks; // This is your $taskStats
    }

    public function getProblematicTasks($project_id)
{
    // You probably already have getTaskStats
    $taskStats = $this->getTaskStats($project_id);

    // Thresholds
    $failRateThreshold = 30;   // % fail is "high"
    $stddevThreshold   = 20;   // Stddev in time is "high"
    $lowSuccessThresh  = 60;   // % success is "low"

    $problematicTasks = [];
    foreach ($taskStats as $task) {
        $isProblem = false;
        $reasons = [];

        if ($task['fail_rate'] > $failRateThreshold) {
            $isProblem = true;
            $reasons[] = "High fail rate ({$task['fail_rate']}%)";
        }
        if ($task['success_rate'] < $lowSuccessThresh) {
            $isProblem = true;
            $reasons[] = "Low success rate ({$task['success_rate']}%)";
        }
        if ($task['stddev_time'] > $stddevThreshold) {
            $isProblem = true;
            $reasons[] = "High time variability (Std Dev {$task['stddev_time']})";
        }
        if ($isProblem) {
            $problematicTasks[] = [
                'task_text' => $task['task_text'],
                'fail_rate' => $task['fail_rate'],
                'success_rate' => $task['success_rate'],
                'stddev_time' => $task['stddev_time'],
                'reasons' => $reasons
            ];
        }
    }
    return $problematicTasks;
}


public function getQuestionStats($project_id)
{
    // Get all test IDs
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');

    if (empty($testIds)) {
        $testIds = [0]; // prevent empty IN clause
    }

    $placeholders = implode(',', array_fill(0, count($testIds), '?'));

    // Get all questionnaire questions for this project
    $stmt = $this->pdo->prepare("
        SELECT q.id, q.text, q.question_type, q.question_options, q.preset_type
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

        foreach (explode(';', $q['question_options']) as $pair) {
            if (strpos($pair, ':') !== false) {
                [$label, $value] = explode(':', $pair, 2);
                $options[trim($value)] = trim($label);
            }
        }

        $sql = "
            SELECT r.answer
            FROM responses r
            JOIN evaluations e ON e.id = r.evaluation_id
            WHERE r.type IN ('question', 'questionnaire')
              AND e.test_id IN ($placeholders)
              AND r.question_id = ?
        ";
        $params = array_merge($testIds, [$questionId]);
        $stmt2 = $this->pdo->prepare($sql);
        $stmt2->execute($params);
        $answers = array_column($stmt2->fetchAll(PDO::FETCH_ASSOC), 'answer');

        $counts = [];
        foreach ($answers as $ans) {
            if (is_string($ans) && strpos($ans, ',') !== false && $type === 'checkbox') {
                foreach (explode(',', $ans) as $subAns) {
                    $subAns = trim($subAns);
                    if ($subAns !== '') {
                        $counts[$subAns] = ($counts[$subAns] ?? 0) + 1;
                    }
                }
            } else {
                $ans = trim($ans);
                if ($ans !== '') {
                    $counts[$ans] = ($counts[$ans] ?? 0) + 1;
                }
            }
        }

        $responseCount = array_sum($counts);
        $uniqueOptions = count($counts);
        $mean = $uniqueOptions > 0 ? ($responseCount / $uniqueOptions) : 0;
        $variance = 0;
        if ($uniqueOptions > 0) {
            foreach ($counts as $val) {
                $variance += pow($val - $mean, 2);
            }
            $variance = round($variance / $uniqueOptions, 2);
        }

        $questionStats[] = [
            'text' => $text,
            'type' => $type,
            'options' => $options,
            'counts' => $counts,
            'variance' => $variance,
            'inconsistent' => $variance >= 5,
            'is_sus' => ($q['preset_type'] ?? '') === 'SUS'
        ];
    }
    return $questionStats;
}

public function getSUSBreakdown($project_id)
{
    // Get test IDs
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    $testIdList = $testIds ? implode(',', $testIds) : '0';

    // Find questionnaire groups with exactly 10 SUS questions
    $stmt = $this->pdo->prepare("
        SELECT qg.id AS group_id
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
    ");
    $stmt->execute([$project_id]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $susBreakdown = [];

    foreach ($groups as $group) {
        $groupId = $group['group_id'];

        // Get 10 SUS questions for this group
        $stmt = $this->pdo->prepare("
            SELECT id, text 
            FROM questions
            WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
            ORDER BY position ASC
        ");
        $stmt->execute([$groupId]);
        $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($susQuestions) !== 10) continue;

        // Get all responses to these questions
        $stmt = $this->pdo->prepare("
            SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer
            FROM evaluations e
            JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN ($testIdList)
        ");
        $stmt->execute();
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
                $susScore = $score * 2.5;
                $label = $susScore >= 85 ? 'Excellent' : ($susScore >= 70 ? 'Good' : ($susScore >= 50 ? 'OK' : 'Poor'));

                $susBreakdown[] = [
                    'participant' => $participant,
                    'answers' => $individualAnswers,
                    'score' => $susScore,
                    'label' => $label
                ];
            }
        }
    }

    return $susBreakdown;
}
public function getParticipantAnalysis($project_id)
{
    // Get all test IDs
    $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
    $testIdList = $testIds ? implode(',', $testIds) : '0';

    // Load participants
    $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get custom fields
    $stmt = $this->pdo->prepare("SELECT id, label FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
    $stmt->execute([$project_id]);
    $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map custom field values
    $participantIds = array_column($participants, 'id');
    $customData = [];
    if (!empty($participantIds)) {
        $placeholders = implode(',', array_fill(0, count($participantIds), '?'));
        $stmt = $this->pdo->prepare("SELECT participant_id, field_id, value FROM participant_custom_data WHERE participant_id IN ($placeholders)");
        $stmt->execute($participantIds);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $customData[$row['participant_id']][$row['field_id']] = $row['value'];
        }
    }

    // Get all SUS question texts (preset_type = 'SUS')
    $stmt = $this->pdo->prepare("
        SELECT text
        FROM questions
        WHERE questionnaire_group_id IN (
            SELECT id FROM questionnaire_groups WHERE test_id IN ($testIdList)
        ) AND preset_type = 'SUS'
    ");
    $stmt->execute();
    $susQuestions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'text');

    $results = [];
    foreach ($participants as $p) {
        $participantId = $p['id'];

        // Get evaluations for this participant (tasks + questionnaire)
        $stmt = $this->pdo->prepare("
            SELECT id, did_tasks, did_questionnaire
            FROM evaluations
            WHERE test_id IN ($testIdList) AND participant_name = ?
        ");
        $stmt->execute([$p['participant_name']]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate task success
        $totalTasks = 0;
        $successTasks = 0;
        foreach ($evaluations as $eval) {
            $stmt = $this->pdo->prepare("
                SELECT evaluation_errors
                FROM responses
                WHERE evaluation_id = ? AND type = 'task'
            ");
            $stmt->execute([$eval['id']]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalTasks += count($tasks);
            foreach ($tasks as $task) {
                if (empty($task['evaluation_errors'])) {
                    $successTasks++;
                }
            }
        }
        $taskSuccessRate = $totalTasks > 0 ? round(($successTasks / $totalTasks) * 100, 1) : 0;

        // SUS score
        $susScore = null;
        if (!empty($susQuestions)) {
            $stmt = $this->pdo->prepare("
                SELECT r.answer, r.question
                FROM responses r
                JOIN evaluations e ON e.id = r.evaluation_id
                WHERE e.test_id IN ($testIdList) 
                AND e.participant_name = ?
                AND r.type IN ('question', 'questionnaire')
                AND r.question IN (" . implode(',', array_fill(0, count($susQuestions), '?')) . ")
                AND r.answer BETWEEN 1 AND 5
            ");
            $params = array_merge([$p['participant_name']], $susQuestions);
            $stmt->execute($params);
            $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $answers = array_column($responses, 'answer');
            if (count($answers) === 10) {
                $score = 0;
                foreach ($answers as $i => $answer) {
                    $score += ($i % 2 === 0) ? ($answer - 1) : (5 - $answer);
                }
                $susScore = round($score * 2.5, 1);
            }
        }

        // Add participant
        $participantData = [
            'participant_name' => $p['participant_name'] ?? 'Anonymous',
            'participant_age' => $p['participant_age'],
            'participant_gender' => $p['participant_gender'],
            'participant_academic_level' => $p['participant_academic_level'],
            'tasks_completed' => $totalTasks,
            'task_success' => $taskSuccessRate,
            'questionnaire_completed' => !empty(array_filter(array_column($evaluations, 'did_questionnaire'))),
            'sus_score' => $susScore,
            'custom_fields' => []
        ];

        foreach ($customFields as $field) {
            $participantData['custom_fields'][$field['label']] = $customData[$participantId][$field['id']] ?? null;
        }

        $results[] = $participantData;
    }

    return $results;
}


}
