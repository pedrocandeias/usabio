<?php

class SessionController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) { 
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        $this->pdo = $pdo;
    }

    public function dashboard()
    {
        $moderatorId = $_SESSION['user_id'];
        $isAdmin = $_SESSION['is_admin'] ?? false;
        $isSuperadmin = $_SESSION['is_superadmin'] ?? false;
        $project_id = $_GET['project_id'] ?? null;

        // CASE A: Show list of projects to pick from
        if (!$project_id) {
            $query = $isAdmin || $isSuperadmin
            ? "SELECT * FROM projects ORDER BY created_at DESC"
            : "SELECT p.* FROM projects p
               JOIN project_user pu ON pu.project_id = p.id
               WHERE pu.moderator_id = ?
               ORDER BY p.created_at DESC";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($isAdmin || $isSuperadmin ? [] : [$moderatorId]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Send to dashboard view with a project picker mode
            include __DIR__ . '/../views/session/project_list.php';
            return;
        }

        // CASE B: Show tests for selected project
        if (!$isAdmin && !$isSuperadmin) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $moderatorId]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        $stmt = $this->pdo->prepare(
            "
        SELECT t.*, p.product_under_test AS product_name, p.title AS project_name
        FROM tests t
        JOIN projects p ON t.project_id = p.id
        WHERE t.project_id = ?
        ORDER BY t.id DESC
    "
        );
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tests = array_map(function($test) {
            // Use a local PDO connection inside this closure if needed
            $pdo = $this->pdo;
        
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_groups WHERE test_id = ?");
            $stmt->execute([$test['id']]);
            $test['task_group_count'] = $stmt->fetchColumn();
        
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM questionnaire_groups WHERE test_id = ?");
            $stmt->execute([$test['id']]);
            $test['questionnaire_group_count'] = $stmt->fetchColumn();
        
            return $test;
        }, $tests);
            $breadcrumbs = [
            ['label' => 'Moderator Dashboard', 'url' => '', 'active' => true],
            ];

        include __DIR__ . '/../views/session/dashboard.php';
    }
      
    public function startTaskSession()
    {
        $test_id = $_GET['test_id'] ?? 0;
    
        if (!$test_id) {
            echo "Missing test ID.";
            exit;
        }
    
        // Fetch test + project name
        $stmt = $this->pdo->prepare(
            "
            SELECT t.*, t.id AS test_id, p.product_under_test AS project_name 
            FROM tests t 
            JOIN projects p ON p.id = t.project_id 
            WHERE t.id = ?
        "
        );
        $stmt->execute([$test_id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $test_id = $test['test_id'];

        // Get participants assigned to this test
        $stmt = $this->pdo->prepare("
        SELECT p.*
        FROM participants p
        JOIN participant_test pt ON pt.participant_id = p.id
        WHERE pt.test_id = ?
        ORDER BY p.participant_name
        ");
        $stmt->execute([$test_id]);
        $rawParticipants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get custom field values for all assigned participants
        $participant_ids = array_column($rawParticipants, 'id');
        $customDataByParticipant = [];

        if (!empty($participant_ids)) {
        $in = implode(',', array_fill(0, count($participant_ids), '?'));
        $stmt = $this->pdo->prepare("
            SELECT * FROM participant_custom_data
            WHERE participant_id IN ($in)
        ");
        $stmt->execute($participant_ids);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $customDataByParticipant[$row['participant_id']][$row['field_id']] = $row['value'];
        }
        }

        // Merge data into $assignedParticipants
        $assignedParticipants = [];
        foreach ($rawParticipants as $p) {
        $p['custom_fields'] = $customDataByParticipant[$p['id']] ?? [];
        $assignedParticipants[] = $p;
        }

// Who already did tasks
$stmt = $this->pdo->prepare("SELECT DISTINCT participant_name FROM evaluations WHERE test_id = ? AND did_tasks = 1");
$stmt->execute([$test['id']]);
$taskCompletedNames = array_map('strtolower', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'participant_name'));

// Who already did questionnaire
$stmt = $this->pdo->prepare("SELECT DISTINCT participant_name FROM evaluations WHERE test_id = ? AND did_questionnaire = 1");
$stmt->execute([$test['id']]);
$questionnaireCompletedNames = array_map('strtolower', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'participant_name'));




        if (!$test) {
            echo "Test not found.";
            exit;
        }
    
         // Optional: prefill participant logic...
        $prefillCustomData = [];
        $previousEvaluation = [];

        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$test_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $test['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $test['project_id'], 'active' => false],
            ['label' => $test['title'], 'url' => '/index.php?controller=Test&action=show&id=' . $test['id'], 'active' => false],
            ['label' => 'Start Task Session', 'url' => '', 'active' => true],
        ];
        
        include __DIR__ . '/../views/session/start_task_session.php';
    }

    
    public function beginTaskSession()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /index.php?controller=Session&action=dashboard');
        exit;
    }

    $test_id = $_POST['test_id'] ?? null;
    $project_id = $_POST['project_id'] ?? null;
    $participant_id = $_POST['participant_id'] ?? null;

    if (!$test_id) {
        echo "Missing test ID.";
        exit;
    }

    // Defaults
    $name = null;
    $age = null;
    $gender = null;
    $academic_level = null;

    if ($participant_id) {
        // Existing participant
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$participant) {
            echo "Participant not found.";
            exit;
        }
        $name = $participant['participant_name'];
        $age = $participant['participant_age'];
        $gender = $participant['participant_gender'];
        $academic_level = $participant['participant_academic_level'];
    } else {
        // New participant
        $name = trim($_POST['participant_name'] ?? '');
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $academic_level = $_POST['participant_academic_level'] ?? null;

        $stmt = $this->pdo->prepare("
            INSERT INTO participants (project_id, test_id, participant_name, participant_age, participant_gender, participant_academic_level, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$project_id, $test_id, $name, $age, $gender, $academic_level]);
        $participant_id = $this->pdo->lastInsertId();

        // Assign participant to test
        $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
        $stmt->execute([$participant_id, $test_id]);

        // Save participant custom fields
        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare("
                INSERT INTO participant_custom_data (participant_id, field_id, value)
                VALUES (?, ?, ?)
            ");
            foreach ($_POST['custom_field'] as $field_id => $value) {
                if (trim($value) !== '') {
                    $stmt->execute([$participant_id, $field_id, $value]);
                }
            }
        }
    }

    $notes = $_POST['moderator_observations'] ?? null;

    // Insert evaluation
    $stmt = $this->pdo->prepare("
    INSERT INTO evaluations (test_id, timestamp, participant_name, participant_age, participant_gender, participant_academic_level, moderator_observations, did_tasks)
        VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $test_id,
        $name,
        $age,
        $gender,
        $academic_level,
        $notes,
        1 // always 1 in beginTaskSession
    ]);

    $evaluation_id = $this->pdo->lastInsertId();

    // Snapshot participant's custom fields
    $stmt = $this->pdo->prepare("SELECT field_id, value FROM participant_custom_data WHERE participant_id = ?");
    $stmt->execute([$participant_id]);
    $custom_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($custom_data)) {
        $stmt = $this->pdo->prepare("INSERT INTO evaluation_custom_data (evaluation_id, field_id, value) VALUES (?, ?, ?)");
        foreach ($custom_data as $row) {
            $stmt->execute([$evaluation_id, $row['field_id'], $row['value']]);
        }
    }

    // Include manually submitted overrides
    if (!empty($_POST['custom_field'])) {
        $stmt = $this->pdo->prepare("INSERT INTO evaluation_custom_data (evaluation_id, field_id, value) VALUES (?, ?, ?)");
        foreach ($_POST['custom_field'] as $field_id => $value) {
            if (trim($value) !== '') {
                $stmt->execute([$evaluation_id, $field_id, $value]);
            }
        }
    }

    header("Location: /index.php?controller=Session&action=trackTasks&evaluation_id=$evaluation_id");
    exit;
}

    public function trackTasks()
    {
        $evaluationId = $_GET['evaluation_id'] ?? 0;

        if (!$evaluationId) {
            echo "Missing evaluation ID.";
            exit;
        }

        // Fetch evaluation and test
        $stmt = $this->pdo->prepare(
            "
        SELECT e.*, t.title AS test_title, t.project_id AS project_id, p.product_under_test AS project_name
        FROM evaluations e
        JOIN tests t ON e.test_id = t.id
        JOIN projects p ON t.project_id = p.id
        WHERE e.id = ?
    "
        );
        $stmt->execute([$evaluationId]);
        $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$evaluation) {
            echo "Session not found.";
            exit;
        }

        // Fetch custom participant fields and their values
        $stmt = $this->pdo->prepare(
            "
            SELECT f.label, d.value
            FROM evaluation_custom_data d
            JOIN participants_custom_fields f ON f.id = d.field_id
            WHERE d.evaluation_id = ?
            ORDER BY f.position ASC
        "
        );
        $stmt->execute([$evaluationId]);
        $customData = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Load task groups + tasks
        $stmt = $this->pdo->prepare(
            "
        SELECT tg.id AS group_id, tg.title AS group_title, tg.position AS group_position,
        t.id AS task_id, t.task_text, t.task_type, t.task_options, t.script, t.scenario, t.preset_type, t.metrics, t.position AS task_position
        FROM task_groups tg
        LEFT JOIN tasks t ON tg.id = t.task_group_id
        WHERE tg.test_id = ?
        ORDER BY tg.position, t.position
    "
        );
        $stmt->execute([$evaluation['test_id']]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Structure: group_id => { title, tasks[] }
        $taskGroups = [];
        foreach ($rows as $row) {
            $gid = $row['group_id'];
            if (!isset($taskGroups[$gid])) {
                $taskGroups[$gid] = [
                'id' => $gid,
                'title' => $row['group_title'],
                'tasks' => []
                ];
            }

            if ($row['task_id']) {
                $taskGroups[$gid]['tasks'][] = [
                    'id' => $row['task_id'],
                    'text' => $row['task_text'],
                    'type' => $row['task_type'],
                    'position' => $row['task_position'],
                    'task_options' => $row['task_options'] ?? '',
                    'script' => $row['script'] ?? '',
                    'scenario' => $row['scenario'] ?? '',
                    'metrics' => $row['metrics'] ?? ''
                    
                ];
            }
        }

        include __DIR__ . '/../views/session/track_tasks.php';
    }

    public function saveTaskResponses()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Session&action=dashboard');
            exit;
        }

        $evaluationId = $_POST['evaluation_id'];
        $timeSpent = $_POST['time_spent'] ?? [];
        $notes = $_POST['notes'] ?? [];

        foreach ($timeSpent as $taskId => $seconds) {
            // Get task text
            $stmt = $this->pdo->prepare("SELECT task_text FROM tasks WHERE id = ?");
            $stmt->execute([$taskId]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$task) { 
                continue;
            }

            $stmt = $this->pdo->prepare(
                "
            INSERT INTO responses (evaluation_id, question, answer, time_spent, evaluation_errors, type)
            VALUES (?, ?, ?, ?, NULL, 'task')
        "
            );
            $stmt->execute(
                [
                $evaluationId,
                $task['task_text'],
                trim($notes[$taskId] ?? ''),
                (int)$seconds
                ]
            );
        }
        $stmt = $this->pdo->prepare("
            SELECT e.*, t.project_id
            FROM evaluations e
            JOIN tests t ON e.test_id = t.id
            WHERE e.id = ?
        ");
        $stmt->execute([$evaluationId]);
        $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
        $project_id = $evaluation['project_id'];

        // Done! Redirect to dashboard or summary
        header("Location: /index.php?controller=Session&action=dashboard&project_id=" . $project_id . "&success=1");
        exit;
    }

    public function startQuestionnaire()
    {
        $test_id = $_GET['test_id'] ?? 0;
    
        if (!$test_id) {
            echo "Missing test ID.";
            exit;
        }
        $minimalLayout = true;
        $stmt = $this->pdo->prepare(
            "
            SELECT t.*, p.product_under_test AS project_name
            FROM tests t
            JOIN projects p ON p.id = t.project_id
            WHERE t.id = ?
            "
        );
        $stmt->execute([$test_id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$test) {
            echo "Test not found.";
            exit;
        }

        $previousEvaluation = null;
        $prefillCustomData = [];

        // Only attempt to prefill if participant name is passed via GET (optional)
        $prefillName = $_GET['name'] ?? null;

        if ($prefillName) {
            $stmt = $this->pdo->prepare(
                "
    SELECT id, participant_name, participant_age, participant_gender, participant_academic_level
    FROM evaluations
    WHERE test_id = ? AND participant_name = ?
    ORDER BY timestamp DESC
    LIMIT 1
"
            );
            $stmt->execute([$test_id, $prefillName]);
            $previousEvaluation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($previousEvaluation) {
                $stmt = $this->pdo->prepare(
                    "
                    SELECT field_id, value FROM evaluation_custom_data
                    WHERE evaluation_id = ?
                "
                );
                $stmt->execute([$previousEvaluation['id']]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $prefillCustomData[$row['field_id']] = $row['value'];
                }
            }
        }

        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$test_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch participants assigned to this test
$stmt = $this->pdo->prepare("
SELECT p.*
FROM participants p
JOIN participant_test pt ON pt.participant_id = p.id
WHERE pt.test_id = ?
ORDER BY p.participant_name
");
$stmt->execute([$test_id]);
$assignedParticipants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Participants who already completed TASKS for this test
$stmt = $this->pdo->prepare("
    SELECT DISTINCT participant_name
    FROM evaluations
    WHERE test_id = ? AND did_tasks = 1
");
$stmt->execute([$test_id]);
$taskCompletedNames = array_map('strtolower', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'participant_name'));

// Participants who already completed the QUESTIONNAIRE for this test
$stmt = $this->pdo->prepare("
    SELECT DISTINCT participant_name
    FROM evaluations
    WHERE test_id = ? AND did_tasks IS NULL
");
$stmt->execute([$test_id]);
$questionnaireCompletedNames = array_map('strtolower', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'participant_name'));

$breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $test['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $test['project_id'], 'active' => false],
            ['label' => $test['title'], 'url' => '/index.php?controller=Test&action=show&id=' . $test['id'], 'active' => false],
            ['label' => 'Start Questionnaire', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/session/start_questionnaire.php';
    }
    
    public function beginQuestionnaire()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /index.php?controller=Session&action=dashboard');
        exit;
    }

    $test_id = $_POST['test_id'] ?? null;
    $project_id = $_POST['project_id'] ?? null;
    $participant_id = $_POST['participant_id'] ?? null;

    if (!$test_id) {
        echo "Missing test ID.";
        exit;
    }

    $name = null;
    $age = null;
    $gender = null;
    $academic_level = null;

    // A: If assigned participant selected
    if ($participant_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {
            echo "Participant not found.";
            exit;
        }

        $name = $participant['participant_name'];
        $age = $participant['participant_age'];
        $gender = $participant['participant_gender'];
        $academic_level = $participant['participant_academic_level'];
    } else {
        // B: Custom/manual or anonymous participant
        $name = trim($_POST['participant_name'] ?? '');
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $academic_level = $_POST['participant_academic_level'] ?? null;

        $stmt = $this->pdo->prepare("
            INSERT INTO participants (
                project_id, test_id, participant_name, participant_age,
                participant_gender, participant_academic_level,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $project_id,
            $test_id,
            $name,
            $age,
            $gender,
            $academic_level
        ]);

        $participant_id = $this->pdo->lastInsertId();

        // Assign to test
        $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
        $stmt->execute([$participant_id, $test_id]);

        // Save participant custom data
        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare("
                INSERT INTO participant_custom_data (participant_id, field_id, value)
                VALUES (?, ?, ?)
            ");
            foreach ($_POST['custom_field'] as $field_id => $value) {
                if (trim($value) !== '') {
                    $stmt->execute([$participant_id, $field_id, $value]);
                }
            }
        }
    }

    $notes = $_POST['moderator_observations'] ?? null;
    $did_tasks = $_POST['did_tasks'] ?? null;

    // C: Save evaluation
    $stmt = $this->pdo->prepare("
        INSERT INTO evaluations (
            test_id, timestamp, participant_name, participant_age,
            participant_gender, participant_academic_level,
            moderator_observations, did_questionnaire
        ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $test_id,
        $name,
        $age,
        $gender,
        $academic_level,
        $notes,
        $did_tasks === 'yes' ? 1 : 0
    ]);

    $evaluation_id = $this->pdo->lastInsertId();

    // D: Snapshot custom data from participant profile
    $stmt = $this->pdo->prepare("
        SELECT field_id, value FROM participant_custom_data
        WHERE participant_id = ?
    ");
    $stmt->execute([$participant_id]);
    $custom_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($custom_data)) {
        $stmt = $this->pdo->prepare("
            INSERT INTO evaluation_custom_data (evaluation_id, field_id, value)
            VALUES (?, ?, ?)
        ");
        foreach ($custom_data as $row) {
            $stmt->execute([$evaluation_id, $row['field_id'], $row['value']]);
        }
    }

    // E: Apply any updated custom field overrides from POST
    if (!empty($_POST['custom_field'])) {
        $stmt = $this->pdo->prepare("
            INSERT INTO evaluation_custom_data (evaluation_id, field_id, value)
            VALUES (?, ?, ?)
        ");
        foreach ($_POST['custom_field'] as $field_id => $value) {
            if (trim($value) !== '') {
                $stmt->execute([$evaluation_id, $field_id, $value]);
            }
        }
    }

    // Redirect to questionnaire tracking
    header("Location: /index.php?controller=Session&action=trackQuestionnaire&evaluation_id=$evaluation_id");
    exit;
}



    public function trackQuestionnaire()
    {
        $evaluationId = $_GET['evaluation_id'] ?? 0;
    
        if (!$evaluationId) {
            echo "Missing evaluation ID.";
            exit;
        }
    
        // Fetch evaluation & test
        $stmt = $this->pdo->prepare(
            "
            SELECT e.*, t.title AS test_title, p.product_under_test AS project_name
            FROM evaluations e
            JOIN tests t ON e.test_id = t.id
            JOIN projects p ON t.project_id = p.id
            WHERE e.id = ?
        "
        );

        
        $stmt->execute([$evaluationId]);
        $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$evaluation) {
            echo "Evaluation not found.";
            exit;
        }
    
        // Get questionnaire groups & questions
        $stmt = $this->pdo->prepare(
            "
            SELECT qg.id AS group_id, qg.title AS group_title, qg.position AS group_position, 
                   q.id AS question_id, q.text, q.question_type, q.question_options, q.position AS question_position, q.preset_type
            FROM questionnaire_groups qg
            LEFT JOIN questions q ON q.questionnaire_group_id = qg.id
            WHERE qg.test_id = ?
            ORDER BY qg.position ASC, q.position ASC
        "
        );
        $stmt->execute([$evaluation['test_id']]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        
        // Group questions
        $questionnaireGroups = [];
        foreach ($rows as $row) {
            $gid = $row['group_id'];
            if (!isset($questionnaireGroups[$gid])) {
                $questionnaireGroups[$gid] = [
                    'id' => $gid,
                    'title' => $row['group_title'],
                    'questions' => []
                ];
            }
    
            if ($row['question_id']) {
                $questionnaireGroups[$gid]['questions'][] = [
                    'id' => $row['question_id'],
                    'text' => $row['text'],
                    'question_type'  => $row['question_type'],
                    'question_options' => $row['question_options'],
                    'preset_type' => $row['preset_type']
                ];
            }
        }
    
        include __DIR__ . '/../views/session/track_questionnaire.php';
    }


    public function saveQuestionnaireResponses()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Session&action=dashboard');
            exit;
        }

        $evaluationId = $_POST['evaluation_id'] ?? null;

        if (!$evaluationId) {
            echo "Missing evaluation ID.";
            exit;
        }

        $answers = $_POST['answer'] ?? [];

        // Loop through each question answered
        foreach ($answers as $questionId => $answer) {
            // Handle checkbox answers (arrays)
            if (is_array($answer)) {
                $answer = implode('; ', $answer);
            }

            // Get the original question text for display (optional)
            $stmt = $this->pdo->prepare("SELECT text FROM questions WHERE id = ?");
            $stmt->execute([$questionId]);
            $questionText = $stmt->fetchColumn() ?? 'Unknown question';

            // Save the response
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO responses (evaluation_id, question, answer, time_spent, evaluation_errors, type)
            VALUES (?, ?, ?, ?, NULL, 'question')
        "
            );
            $stmt->execute(
                [
                $evaluationId,
                $questionText,
                $answer,
                0 // time_spent is not tracked in questionnaires for now
                ]
            );
        }

        // Redirect to confirmation page
        header('Location: /index.php?controller=Session&action=questionnaireComplete');
        exit;
    }

    public function questionnaireComplete()
    {
        include __DIR__ . '/../views/session/questionnaire_complete.php';
    }

}
