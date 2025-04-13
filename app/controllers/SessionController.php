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
        $projectId = $_GET['project_id'] ?? null;

        $breadcrumbs = [
        ['label' => 'Moderator Dashboard', 'url' => '', 'active' => true],
        ];

        // CASE A: Show list of projects to pick from
        if (!$projectId) {
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
            $stmt->execute([$projectId, $moderatorId]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        $stmt = $this->pdo->prepare(
            "
        SELECT t.*, p.product_under_test AS project_name
        FROM tests t
        JOIN projects p ON t.project_id = p.id
        WHERE t.project_id = ?
        ORDER BY t.id DESC
    "
        );
        $stmt->execute([$projectId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tests as &$test) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM task_groups WHERE test_id = ?");
            $stmt->execute([$test['id']]);
            $test['task_group_count'] = $stmt->fetchColumn();

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM questionnaire_groups WHERE test_id = ?");
            $stmt->execute([$test['id']]);
            $test['questionnaire_group_count'] = $stmt->fetchColumn();
        }

        include __DIR__ . '/../views/session/dashboard.php';
    }
      
    public function startTaskSession()
    {
        $testId = $_GET['test_id'] ?? 0;
    
        if (!$testId) {
            echo "Missing test ID.";
            exit;
        }
    
        // Fetch test + project name
        $stmt = $this->pdo->prepare(
            "
            SELECT t.*, p.product_under_test AS project_name 
            FROM tests t 
            JOIN projects p ON p.id = t.project_id 
            WHERE t.id = ?
        "
        );
        $stmt->execute([$testId]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$test) {
            echo "Test not found.";
            exit;
        }
    
         // Optional: prefill participant logic...
        $prefillCustomData = [];
        $previousEvaluation = [];

        $stmt = $this->pdo->prepare("SELECT * FROM test_custom_fields WHERE test_id = ? ORDER BY position ASC");
        $stmt->execute([$testId]);
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

        $testId         = $_POST['test_id'];
        $name           = $_POST['participant_name'];
        $notes          = $_POST['moderator_observations'] ?? null;
        $age            = $_POST['participant_age'] ?? null;
        $gender         = $_POST['participant_gender'] ?? null;
        $academic_level = $_POST['participant_academic_level'] ?? null;
        

        // Create a new evaluation record
        $stmt = $this->pdo->prepare(
            "
        INSERT INTO evaluations (test_id, timestamp, participant_name, participant_age, participant_gender, participant_academic_level, moderator_observations)
        VALUES (?, NOW(), ?, ?, ?, ?, ?)
    "
        );


        $stmt->execute(
            [
            $testId,
            $name,
            $notes,
            $age,
            $gender,
            $academic_level
            ]
        );

        $evaluationId = $this->pdo->lastInsertId();
        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare(
                "
                INSERT INTO evaluation_custom_data (evaluation_id, field_id, value)
                VALUES (?, ?, ?)
            "
            );
        
            foreach ($_POST['custom_field'] as $fieldId => $value) {
                if (trim($value) !== '') {
                    $stmt->execute([$evaluationId, $fieldId, $value]);
                }
            }
        }
        // Redirect to task tracking screen
        header("Location: /index.php?controller=Session&action=trackTasks&evaluation_id=$evaluationId");
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
            echo "Session not found.";
            exit;
        }

        // Fetch custom participant fields and their values
        $stmt = $this->pdo->prepare(
            "
            SELECT f.label, d.value
            FROM evaluation_custom_data d
            JOIN test_custom_fields f ON f.id = d.field_id
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
            INSERT INTO responses (evaluation_id, question, answer, time_spent, evaluation_errors)
            VALUES (?, ?, ?, ?, NULL)
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
        $projectId = $evaluation['project_id'];

        // Done! Redirect to dashboard or summary
        header("Location: /index.php?controller=Session&action=dashboard&project_id=" . $projectId . "&success=1");
        exit;
    }

    public function startQuestionnaire()
    {
        $testId = $_GET['test_id'] ?? 0;
    
        if (!$testId) {
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
        $stmt->execute([$testId]);
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
            $stmt->execute([$testId, $prefillName]);
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

        $stmt = $this->pdo->prepare("SELECT * FROM test_custom_fields WHERE test_id = ? ORDER BY position ASC");
        $stmt->execute([$testId]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch existing participants who completed tasks in this test
        $stmt = $this->pdo->prepare(
            "
            SELECT DISTINCT participant_name
            FROM evaluations
            WHERE test_id = ? AND participant_name IS NOT NULL
            ORDER BY participant_name
        "
        );
        $stmt->execute([$testId]);
        $existingParticipants = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $test['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $test['project_id'], 'active' => false],
            ['label' => $test['title'], 'url' => '/index.php?controller=Test&action=show&id=' . $test['id'], 'active' => false],
            ['label' => 'Start Questionnaire', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/session/start_questionnaire.php';
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

    public function beginQuestionnaire()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Session&action=dashboard');
            exit;
        }

        $testId = $_POST['test_id'];
        $name = trim($_POST['participant_name']);
        $notes = $_POST['moderator_observations'];
        $age = $_POST['participant_age'] ?? null;
        $gender = $_POST['participant_gender'] ?? null;
        $academic_level = $_POST['participant_academic_level'] ?? null;
        $notes = $_POST['moderator_observations'] ?? null;
        $didTasks = $_POST['did_tasks'] ?? null;

        $stmt = $this->pdo->prepare(
            "
       INSERT INTO evaluations (test_id, timestamp, participant_name, participant_age, participant_gender, participant_academic_level, moderator_observations, did_tasks)
        VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)
    "
        );

        $stmt->execute(
            [
            $testId,
            $name,
            $age, 
            $gender,
            $academic_level,
            $notes,
            $didTasks === 'yes' ? 1 : 0
            ]
        );
    

        $evaluationId = $this->pdo->lastInsertId();
        if (!empty($_POST['custom_field'])) {
            $stmt = $this->pdo->prepare(
                "
                INSERT INTO evaluation_custom_data (evaluation_id, field_id, value)
                VALUES (?, ?, ?)
            "
            );
        
            foreach ($_POST['custom_field'] as $fieldId => $value) {
                if (trim($value) !== '') {
                    $stmt->execute([$evaluationId, $fieldId, $value]);
                }
            }
        }
        header("Location: /index.php?controller=Session&action=trackQuestionnaire&evaluation_id=" . $evaluationId);
        exit;
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
            INSERT INTO responses (evaluation_id, question, answer, time_spent)
            VALUES (?, ?, ?, ?)
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
