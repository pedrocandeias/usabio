<?php
require_once __DIR__ . '/BaseController.php'; // carrega o base

class ExportController extends BaseController
{


    protected $exportModel;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
            exit;
        }
        parent::__construct($pdo); // Inicializa $this->pdo antes de usá-lo
        $this->pdo;
    }


    public function index()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Access control
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare(
            "
            SELECT * FROM participants
            WHERE project_id = ?
            ORDER BY participant_name
        "
        );
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project['id'], 'active' => false],
            ['label' => 'Export', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/export/index.php';
    }

    public function demographicsCSV()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // 1. Get custom field definitions for header
        $stmt = $this->pdo->prepare(
            "
            SELECT id, label FROM participants_custom_fields
            WHERE project_id = ?
            ORDER BY position ASC
        "
        );
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map for later: [field_id => label]
        $customFieldLabels = array_column($customFields, 'label', 'id');

        // 2. Get all participants
        $stmt = $this->pdo->prepare(
            "
            SELECT * FROM participants
            WHERE project_id = ?
            ORDER BY participant_name
        "
        );
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Get all custom field values
        $participantIds = array_column($participants, 'id');
        $customData = [];

        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare(
                "
                SELECT participant_id, field_id, value
                FROM participant_custom_data
                WHERE participant_id IN ($in)
            "
            );
            $stmt->execute($participantIds);

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // 4. Set headers for CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=demographics_project_' . $project_id . '.csv');

        $output = fopen('php://output', 'w');

        // 5. Output CSV header
        $header = ['ID', 'Name', 'Age', 'Gender', 'Academic Level'];
        foreach ($customFieldLabels as $label) {
            $header[] = $label;
        }
        fputcsv($output, $header);

        // 6. Output each participant row
        foreach ($participants as $p) {
            $row = [
                $p['id'],
                $p['participant_name'],
                $p['participant_age'],
                $p['participant_gender'],
                $p['participant_academic_level']
            ];

            foreach (array_keys($customFieldLabels) as $fieldId) {
                $row[] = $customData[$p['id']][$fieldId] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function fullCSV()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // 1. Get custom fields (for demographics)
        $stmt = $this->pdo->prepare(
            "
        SELECT id, label FROM participants_custom_fields
        WHERE project_id = ?
        ORDER BY position ASC
    "
        );
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $customFieldLabels = array_column($customFields, 'label', 'id');

        // 2. Get participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ? ORDER BY participant_name");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $participantMap = array_column($participants, null, 'id');

        // 3. Get custom data per participant
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare(
                "
            SELECT * FROM participant_custom_data
            WHERE participant_id IN ($in)
        "
            );
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // 4. Get evaluations for this project
        $stmt = $this->pdo->prepare(
            "
        SELECT e.*, t.project_id
        FROM evaluations e
        JOIN tests t ON t.id = e.test_id
        WHERE t.project_id = ?
        ORDER BY e.timestamp ASC
    "
        );
        $stmt->execute([$project_id]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Get all responses
        $evaluationIds = array_column($evaluations, 'id');
        $responseMap = [];
        $allQuestions = [];

        if (!empty($evaluationIds)) {
            $in = implode(',', array_fill(0, count($evaluationIds), '?'));
            $stmt = $this->pdo->prepare(
                "
            SELECT * FROM responses
            WHERE evaluation_id IN ($in)
        "
            );
            $stmt->execute($evaluationIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $responseMap[$r['evaluation_id']][$r['question']] = $r['answer'];
                $allQuestions[$r['question']] = true;
            }
        }

        // 6. Prepare CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=full_responses_project_' . $project_id . '.csv');
        $output = fopen('php://output', 'w');

        // Header
        $staticCols = ['Participant ID', 'Name', 'Age', 'Gender', 'Academic Level', 'Evaluation Date', 'Did Tasks', 'Did Questionnaire'];
        $customCols = array_values($customFieldLabels);
        $questionCols = array_keys($allQuestions);
        fputcsv($output, array_merge($staticCols, $customCols, $questionCols));

        // Rows
        foreach ($evaluations as $eval) {
            $row = [];

            // Find matching participant by name (or add a lookup later if you add participant_id to evaluations)
            $participant = null;
            foreach ($participants as $p) {
                if ($p['participant_name'] === $eval['participant_name']) {
                    $participant = $p;
                    break;
                }
            }

            $row[] = $participant['id'] ?? '';
            $row[] = $eval['participant_name'];
            $row[] = $eval['participant_age'];
            $row[] = $eval['participant_gender'];
            $row[] = $eval['participant_academic_level'];
            $row[] = $eval['timestamp'];
            $row[] = $eval['did_tasks'] ? 'yes' : 'no';
            $row[] = $eval['did_questionnaire'] ? 'yes' : 'no';

            // Custom fields
            foreach (array_keys($customFieldLabels) as $field_id) {
                $value = $participant['id'] ?? null;
                $row[] = $value ? ($customData[$value][$field_id] ?? '') : '';
            }

            // Responses
            foreach ($questionCols as $q) {
                $row[] = $responseMap[$eval['id']][$q] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }


    public function exportTaskEvaluationsCSV()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Get participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $participantMap = array_column($participants, null, 'participant_name'); // keyed by name

        // Get custom fields
        $stmt = $this->pdo->prepare("SELECT id, label FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $customFieldLabels = array_column($customFields, 'label', 'id');

        // Get participant custom data
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM participant_custom_data WHERE participant_id IN ($in)");
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // Get all task evaluations
        $stmt = $this->pdo->prepare(
            "
        SELECT e.*, t.title AS test_title, t.id AS test_id
        FROM evaluations e
        JOIN tests t ON t.id = e.test_id
        WHERE t.project_id = ? AND e.did_tasks = 1
    "
        );
        $stmt->execute([$project_id]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get responses
        $evaluationIds = array_column($evaluations, 'id');
        $responseMap = [];
        $allQuestions = [];

        if (!empty($evaluationIds)) {
            $in = implode(',', array_fill(0, count($evaluationIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM responses WHERE evaluation_id IN ($in)");
            $stmt->execute($evaluationIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $responseMap[$r['evaluation_id']][$r['question']] = $r['answer'];
                $allQuestions[$r['question']] = true;
            }
        }

        // Output CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=task_responses_project_' . $project_id . '.csv');
        $output = fopen('php://output', 'w');

        $header = ['Test ID', 'Test Title', 'Participant Name', 'Age', 'Gender', 'Academic Level', 'Timestamp'];
        foreach ($customFieldLabels as $label) {
            $header[] = $label;
        }
        foreach (array_keys($allQuestions) as $q) {
            $header[] = $q;
        }
        fputcsv($output, $header);

        foreach ($evaluations as $eval) {
            $row = [
                $eval['test_id'],
                $eval['test_title'],
                $eval['participant_name'],
                $eval['participant_age'],
                $eval['participant_gender'],
                $eval['participant_academic_level'],
                $eval['timestamp']
            ];

            $participant = $participantMap[$eval['participant_name']] ?? null;
            $pid = $participant['id'] ?? null;

            foreach (array_keys($customFieldLabels) as $fieldId) {
                $row[] = $pid ? ($customData[$pid][$fieldId] ?? '') : '';
            }

            foreach (array_keys($allQuestions) as $q) {
                $row[] = $responseMap[$eval['id']][$q] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function exportQuestionnaireEvaluationsCSV()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Get participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $participantMap = array_column($participants, null, 'participant_name');

        // Get custom fields
        $stmt = $this->pdo->prepare("SELECT id, label FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $customFieldLabels = array_column($customFields, 'label', 'id');

        // Get participant custom data
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM participant_custom_data WHERE participant_id IN ($in)");
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // Get questionnaire evaluations
        $stmt = $this->pdo->prepare(
            "
        SELECT e.*, t.title AS test_title, t.id AS test_id
        FROM evaluations e
        JOIN tests t ON t.id = e.test_id
        WHERE t.project_id = ? AND e.did_questionnaire = 1
    "
        );
        $stmt->execute([$project_id]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get responses
        $evaluationIds = array_column($evaluations, 'id');
        $responseMap = [];
        $allQuestions = [];

        if (!empty($evaluationIds)) {
            $in = implode(',', array_fill(0, count($evaluationIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM responses WHERE evaluation_id IN ($in)");
            $stmt->execute($evaluationIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $responseMap[$r['evaluation_id']][$r['question']] = $r['answer'];
                $allQuestions[$r['question']] = true;
            }
        }

        // Output CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=questionnaire_responses_project_' . $project_id . '.csv');
        $output = fopen('php://output', 'w');

        $header = ['Test ID', 'Test Title', 'Participant Name', 'Age', 'Gender', 'Academic Level', 'Timestamp'];
        foreach ($customFieldLabels as $label) {
            $header[] = $label;
        }
        foreach (array_keys($allQuestions) as $q) {
            $header[] = $q;
        }
        fputcsv($output, $header);

        foreach ($evaluations as $eval) {
            $row = [
                $eval['test_id'],
                $eval['test_title'],
                $eval['participant_name'],
                $eval['participant_age'],
                $eval['participant_gender'],
                $eval['participant_academic_level'],
                $eval['timestamp']
            ];

            $participant = $participantMap[$eval['participant_name']] ?? null;
            $pid = $participant['id'] ?? null;

            foreach (array_keys($customFieldLabels) as $fieldId) {
                $row[] = $pid ? ($customData[$pid][$fieldId] ?? '') : '';
            }

            foreach (array_keys($allQuestions) as $q) {
                $row[] = $responseMap[$eval['id']][$q] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function exportTaskResponsesByTest()
    {
        $test_id = $_GET['test_id'] ?? 0;

        // Get test info
        $stmt = $this->pdo->prepare("SELECT t.*, p.title AS project_title FROM tests t JOIN projects p ON p.id = t.project_id WHERE t.id = ?");
        $stmt->execute([$test_id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            echo "Test not found.";
            exit;
        }

        // Get task evaluations
        $stmt = $this->pdo->prepare(
            "
        SELECT * FROM evaluations
        WHERE test_id = ? AND did_tasks = 1
    "
        );
        $stmt->execute([$test_id]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get responses
        $evaluationIds = array_column($evaluations, 'id');
        $responseMap = [];
        $allQuestions = [];

        if (!empty($evaluationIds)) {
            $in = implode(',', array_fill(0, count($evaluationIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM responses WHERE evaluation_id IN ($in)");
            $stmt->execute($evaluationIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $responseMap[$r['evaluation_id']][$r['question']] = $r['answer'];
                $allQuestions[$r['question']] = true;
            }
        }

        // Output CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=task_responses_test_' . $test_id . '.csv');
        $output = fopen('php://output', 'w');

        $header = ['Test ID', 'Test Title', 'Participant Name', 'Age', 'Gender', 'Academic Level', 'Timestamp'];
        foreach (array_keys($allQuestions) as $q) {
            $header[] = $q;
        }
        fputcsv($output, $header);

        foreach ($evaluations as $eval) {
            $row = [
                $test['id'],
                $test['title'],
                $eval['participant_name'],
                $eval['participant_age'],
                $eval['participant_gender'],
                $eval['participant_academic_level'],
                $eval['timestamp']
            ];

            foreach (array_keys($allQuestions) as $q) {
                $row[] = $responseMap[$eval['id']][$q] ?? '';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function susCSV()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Fetch all SUS questions in this project
        $stmt = $this->pdo->prepare(
            "
        SELECT qg.id AS group_id
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
    "
        );
        $stmt->execute([$project_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $susBreakdown = [];

        foreach ($groups as $group) {
            $groupId = $group['group_id'];

            // Get SUS questions
            $stmt = $this->pdo->prepare(
                "
            SELECT id, text FROM questions
            WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
            ORDER BY position ASC
        "
            );
            $stmt->execute([$groupId]);
            $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($susQuestions) !== 10) {
                continue;
            }

            // Fetch all responses to these questions
            $stmt = $this->pdo->prepare(
                "
            SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer
            FROM evaluations e
            JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN (SELECT id FROM tests WHERE project_id = ?)
        "
            );
            $stmt->execute([$project_id]);
            $allResponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group responses by evaluation
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

        // Output CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=sus_scores_project_' . $project_id . '.csv');
        $output = fopen('php://output', 'w');

        // Header
        $header = ['Participant', 'SUS Score', 'Label'];
        for ($i = 1; $i <= 10; $i++) {
            $header[] = "Q$i";
        }
        fputcsv($output, $header);

        // Rows
        foreach ($susBreakdown as $entry) {
            $row = [
                $entry['participant'],
                $entry['score'],
                $entry['label']
            ];
            foreach ($entry['answers'] as $a) {
                $row[] = $a;
            }
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function exportProjectJSON()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Get project
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // Get all tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tests as &$test) {
            // Task Groups
            $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id = ? ORDER BY position ASC");
            $stmt->execute([$test['id']]);
            $test['task_groups'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($test['task_groups'] as &$tg) {
                $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ? ORDER BY position ASC");
                $stmt->execute([$tg['id']]);
                $tg['tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Questionnaire Groups
            $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id = ? ORDER BY position ASC");
            $stmt->execute([$test['id']]);
            $test['questionnaire_groups'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($test['questionnaire_groups'] as &$qg) {
                $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE questionnaire_group_id = ? ORDER BY position ASC");
                $stmt->execute([$qg['id']]);
                $qg['questions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        // Get participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get custom fields
        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $fieldMap = [];
        foreach ($customFields as $f) {
            $fieldMap[$f['id']] = $f;
        }

        // Get custom data
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM participant_custom_data WHERE participant_id IN ($in)");
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        // Merge custom data into participants
        foreach ($participants as &$p) {
            $p['custom_fields'] = [];
            foreach ($customData[$p['id']] ?? [] as $field_id => $value) {
                $label = $fieldMap[$field_id]['label'] ?? 'unknown';
                $p['custom_fields'][$label] = $value;
            }
        }

        // Build structure
        $output = [
            'project' => $project,
            'tests' => $tests,
            'participants' => $participants,
            'custom_fields' => $customFields
        ];

        // Output
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=project_' . $project_id . '.json');
        echo json_encode($output, JSON_PRETTY_PRINT);
        exit;
    }

    public function printProject()
    {
        $project_id = $_GET['project_id'] ?? 0;

        // Access control
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Load project
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // Load tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load tasks
        $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id IN (SELECT id FROM tests WHERE project_id = ?) ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $taskGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tasksByGroup = [];
        foreach ($taskGroups as $group) {
            $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ? ORDER BY position ASC");
            $stmt->execute([$group['id']]);
            $tasksByGroup[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Load questionnaire groups
        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id IN (SELECT id FROM tests WHERE project_id = ?) ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $questionnaireGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $questionsByGroup = [];
        foreach ($questionnaireGroups as $group) {
            $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE questionnaire_group_id = ? ORDER BY position ASC");
            $stmt->execute([$group['id']]);
            $questionsByGroup[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Load participants
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE project_id = ? ORDER BY participant_name ASC");
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load custom field labels
        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load participant custom data
        $participantIds = array_column($participants, 'id');
        $customData = [];
        if (!empty($participantIds)) {
            $in = implode(',', array_fill(0, count($participantIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM participant_custom_data WHERE participant_id IN ($in)");
            $stmt->execute($participantIds);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $customData[$row['participant_id']][$row['field_id']] = $row['value'];
            }
        }

        include __DIR__ . '/../views/export/print_project.php';
    }

public function exportProjectPdf()
{
         $project_id = $_GET['project_id'] ?? 0;

    // Access control
    if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
        $stmt->execute([$project_id, $_SESSION['user_id']]);
        if (!$stmt->fetchColumn()) {
            echo "Access denied.";
            exit;
        }
    }

    // === Gather analysis data (you can refactor this to a helper or just reuse your AnalysisController logic) ===
    require_once __DIR__ . '/../controllers/AnalysisController.php';
    $analysis = new AnalysisController($this->pdo);

    // Project info
    $project = $analysis->getProject($project_id);

    // AI summary (latest if available)
    $stmt = $this->pdo->prepare("SELECT ai_summary, last_updated FROM usability_results WHERE project_id = ? ORDER BY last_updated DESC LIMIT 1");
    $stmt->execute([$project_id]);
    $aiSummary = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // --- Task analytics ---
    ob_start();
    $analysis->tasks(); // This will set up $taskStats, $chartData etc.
    ob_end_clean();
    $taskStats = isset($GLOBALS['taskStats']) ? $GLOBALS['taskStats'] : [];

    // --- Problematic tasks ---
    $problematicTasks = isset($GLOBALS['problematicTasks']) ? $GLOBALS['problematicTasks'] : [];

    // --- Questionnaire analytics ---
    ob_start();
    $analysis->questionnaires();
    ob_end_clean();
    $questionStats = isset($GLOBALS['questionStats']) ? $GLOBALS['questionStats'] : [];

    // --- SUS breakdown ---
    ob_start();
    $analysis->sus();
    ob_end_clean();
    $susBreakdown = isset($GLOBALS['susBreakdown']) ? $GLOBALS['susBreakdown'] : [];

    // --- Participant analytics ---
    ob_start();
    $analysis->participants();
    ob_end_clean();
    $participants = isset($GLOBALS['participants']) ? $GLOBALS['participants'] : [];
    $customFields = isset($GLOBALS['customFields']) ? $GLOBALS['customFields'] : [];
    $correlationData = isset($GLOBALS['correlationData']) ? $GLOBALS['correlationData'] : [];

    // ===== Render HTML view for PDF =====
    ob_start();
    include __DIR__ . '/../views/export/pdf_report.php';
    $html = ob_get_clean();

    // ===== Generate PDF (mPDF) =====
    require_once __DIR__ . '/../../vendor/autoload.php'; // adjust path if needed
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->WriteHTML($html);
    $filename = 'usabio_project_' . $project_id . '_report_' . date('Ymd_His') . '.pdf';
    $mpdf->Output($filename, 'D'); // 'D' = Download, 'I' = inline
    exit;
    }

}