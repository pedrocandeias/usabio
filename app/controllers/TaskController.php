<?php

class TaskController
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

    private function userCanAccessGroup($groupId)
    {
        if ($_SESSION['is_admin']) { return true;
        }

        $stmt = $this->pdo->prepare(
            "
            SELECT p.id
            FROM projects p
            JOIN tests t ON t.project_id = p.id
            JOIN task_groups g ON g.test_id = t.id
            JOIN project_user pu ON pu.project_id = p.id
            WHERE g.id = ? AND pu.moderator_id = ?
        "
        );
        $stmt->execute([$groupId, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }

    public function index()
    {
        $groupId = $_GET['group_id'] ?? 0;
    
        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }
    
        // Get test_id and project_id from the group
        $stmt = $this->pdo->prepare(
            "
            SELECT t.test_id, ts.project_id, ts.title AS test_title
            FROM task_groups t
            JOIN tests ts ON t.test_id = ts.id
            WHERE t.id = ?
        "
        );
        $stmt->execute([$groupId]);
        $meta = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$meta) {
            echo "Invalid group.";
            exit;
        }
    
        $testId = $meta['test_id'];
        $projectId = $meta['project_id'];
        $testTitle = $meta['test_title'];
    
        // Get tasks
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ? ORDER BY position ASC");
        $stmt->execute([$groupId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

        $stmt = $this->pdo->prepare("
        SELECT tg.title AS group_title, t.id AS test_id, t.title AS test_title, p.product_under_test AS project_name
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        JOIN projects p ON p.id = t.project_id
        WHERE tg.id = ?
    ");
    $stmt->execute([$groupId]);
    $context = $stmt->fetch(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/tasks/index.php';
    }
    

    public function create()
    {
        $groupId = $_GET['group_id'] ?? 0;
        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }

        $task = [
            'id' => 0,
            'task_group_id' => $groupId,
            'task_text' => '',
            'script' => '',
            'scenario' => '',
            'metrics' => '',
            'task_type' => 'text',
            'task_options' => '',
            'position' => 0
        ];

        $stmt = $this->pdo->prepare("
        SELECT tg.title AS group_title, t.id AS test_id, t.title AS test_title, p.product_under_test AS project_name
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        JOIN projects p ON p.id = t.project_id
        WHERE tg.id = ?
    ");
    $stmt->execute([$groupId]);
    $context = $stmt->fetch(PDO::FETCH_ASSOC);

    $testId = $context['test_id'];
        include __DIR__ . '/../views/tasks/form.php';
    }

    public function store()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare(
            "
            INSERT INTO tasks (task_group_id, task_text, script, scenario, metrics, task_type, task_options, position, preset_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
        );
        $stmt->execute(
            [
            $data['task_group_id'],
            $data['task_text'],
            $data['script'],
            $data['scenario'],
            $data['metrics'],
            $data['task_type'],
            $data['task_options'],
            $data['position'] ?? 0,
            $data['preset_type'] ?? null
            ]
        );
        $taskGroupId = $data['task_group_id'];
        $stmt = $this->pdo->prepare("
        SELECT t.id AS test_id
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        WHERE tg.id = ?
    ");
    $stmt->execute([$data['task_group_id']]);
    $testId = $stmt->fetchColumn();

        header("Location: /index.php?controller=Test&action=show&id=" . $testId. "#taskgroup" . $taskGroupId);
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        $groupId = $task['task_group_id'];

        $stmt = $this->pdo->prepare("
        SELECT tg.title AS group_title, t.id AS test_id, t.title AS test_title, p.product_under_test AS project_name
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        JOIN projects p ON p.id = t.project_id
        WHERE tg.id = ?
    ");
    $stmt->execute([$groupId]);
    $context = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$task || !$this->userCanAccessGroup($task['task_group_id'])) {
            echo "Access denied or task not found.";
            exit;
        }

        include __DIR__ . '/../views/tasks/form.php';
    }

    public function reorder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $groupId = $data['group_id'] ?? 0;
        $order = $data['order'] ?? [];
    
        if (!$this->userCanAccessGroup($groupId)) {
            http_response_code(403);
            echo "Access denied";
            exit;
        }
    
        foreach ($order as $position => $taskId) {
            $stmt = $this->pdo->prepare("UPDATE tasks SET position = ? WHERE id = ?");
            $stmt->execute([$position, $taskId]);
        }
    

        http_response_code(204);
    }
    

    public function update()
    {
        $data = $_POST;
        $stmt = $this->pdo->prepare(
            "
            UPDATE tasks SET
                task_text = ?, script = ?, scenario = ?, metrics = ?, task_type = ?, task_options = ?, position = ?, preset_type = ?
            WHERE id = ?
        "
        );
        $stmt->execute(
            [
            $data['task_text'],
            $data['script'],
            $data['scenario'],
            $data['metrics'],
            $data['task_type'],
            $data['task_options'],
            $data['position'],
            $data['preset_type'] ?? null,
            $data['id']
            ]
        );
        $taskGroupId = $data['task_group_id'];

        $stmt = $this->pdo->prepare("
            SELECT t.id AS test_id
            FROM task_groups tg
            JOIN tests t ON t.id = tg.test_id
            WHERE tg.id = ?
        ");
        $stmt->execute([$data['task_group_id']]);
        $testId = $stmt->fetchColumn();

        header("Location: /index.php?controller=Test&action=show&id=" . $testId. "#taskgroup". $taskGroupId);
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT task_group_id FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $groupId = $stmt->fetchColumn();

        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Task&action=index&group_id=" . $groupId);
        exit;
    }
}
