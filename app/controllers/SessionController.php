<?php

class SessionController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start();
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

        // Get all tests for this user (assigned via project_user)
        $query = $isAdmin
            ? "SELECT t.*, p.product_under_test AS project_name
               FROM tests t
               JOIN projects p ON t.project_id = p.id
               ORDER BY p.id DESC"
            : "SELECT t.*, p.product_under_test AS project_name
               FROM tests t
               JOIN projects p ON t.project_id = p.id
               JOIN project_user pu ON pu.project_id = p.id
               WHERE pu.moderator_id = ?
               ORDER BY p.id DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($isAdmin ? [] : [$moderatorId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count tasks and questionnaires per test
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

        // Fetch test to show on form
        $stmt = $this->pdo->prepare("SELECT t.*, p.product_under_test AS project_name FROM tests t JOIN projects p ON p.id = t.project_id WHERE t.id = ?");
        $stmt->execute([$testId]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            echo "Test not found.";
            exit;
        }

        include __DIR__ . '/../views/session/start_task_session.php';
    }

    public function beginTaskSession()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Session&action=dashboard');
            exit;
        }

        $testId = $_POST['test_id'];
        $name = $_POST['participant_name'];
        $notes = $_POST['moderator_observations'];

        // Create a new evaluation record
        $stmt = $this->pdo->prepare(
            "
        INSERT INTO evaluations (test_id, timestamp, participant_name, moderator_observations)
        VALUES (?, NOW(), ?, ?)
    "
        );
        $stmt->execute([$testId, $name, $notes]);

        $evaluationId = $this->pdo->lastInsertId();

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

        // Load task groups + tasks
        $stmt = $this->pdo->prepare(
            "
        SELECT tg.id AS group_id, tg.title AS group_title, tg.position AS group_position,
       t.id AS task_id, t.task_text, t.task_type, t.task_options, t.script, t.scenario, t.metrics, t.position AS task_position
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
            if (!$task) { continue;
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

        // Done! Redirect to dashboard or summary
        header("Location: /index.php?controller=Session&action=dashboard&success=1");
        exit;
    }

}
