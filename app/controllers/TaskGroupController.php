<?php

class TaskGroupController
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

    private function userCanAccessTest($testId)
    {
        if ($_SESSION['is_admin']) { return true;
        }

        $stmt = $this->pdo->prepare(
            "
            SELECT p.id FROM projects p
            JOIN tests t ON t.project_id = p.id
            JOIN project_user pu ON pu.project_id = p.id
            WHERE t.id = ? AND pu.moderator_id = ?
        "
        );
        $stmt->execute([$testId, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }

    public function index()
    {
        $testId = $_GET['test_id'] ?? 0;

        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id = ? ORDER BY position ASC, id ASC");
        $stmt->execute([$testId]);
        $taskGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch test + project info
        $stmt = $this->pdo->prepare(
            "
        SELECT t.title, p.product_under_test AS project_name
        FROM tests t
        JOIN projects p ON t.project_id = p.id
        WHERE t.id = ?
        "
        );
        $stmt->execute([$testId]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/task_groups/index.php';
    }

    public function create()
    {
        $testId = $_GET['test_id'] ?? 0;

        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $taskGroup = [
        'id' => 0,
        'test_id' => $testId,
        'title' => '',
        'position' => 0
        ];

        // Load context
        $stmt = $this->pdo->prepare(
            "
        SELECT t.title AS test_title, t.id AS test_id, p.id AS project_id, p.product_under_test AS project_name
        FROM tests t
        JOIN projects p ON p.id = t.project_id
        WHERE t.id = ?
 "
        );
        $stmt->execute([$testId]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);

        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
        ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $testId.'#task-group-list', 'active' => false],
        ['label' => 'Create Task Group', 'url' => '', 'active' => true],
        ];
        include __DIR__ . '/../views/task_groups/form.php';
    }

    public function store()
    {
        $data = $_POST;
        $testId = $data['test_id'] ?? 0;
        $stmt = $this->pdo->prepare("INSERT INTO task_groups (test_id, title, position) VALUES (?, ?, ?)");
        $stmt->execute(
            [
            $data['test_id'],
            $data['title'],
            $data['position'] ?? 0
            ]
        );

        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#task-group-list");
        exit;
    }

    public function edit()
    {
        
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE id = ?");
        $stmt->execute([$id]);
        $taskGroup = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $testId = $taskGroup['test_id'];
       $stmt = $this->pdo->prepare("
        SELECT tg.*, t.title AS test_title, tg.id AS task_group_id, tg.title AS group_title, t.id AS test_id, p.id AS project_id, p.product_under_test AS project_name
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        JOIN projects p ON p.id = t.project_id
        WHERE tg.id = ?
    ");
    $stmt->execute([$id]);
    $context = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$taskGroup || !$this->userCanAccessTest($taskGroup['test_id'])) {
            echo "Access denied or group not found.";
            exit;
        }

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
            ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'].'#task-group-list', 'active' => false],
            ['label' => $context['group_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'].'#taskgroup'.$context['task_group_id'], 'active' => false],
            ['label' => 'Tasks', 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'].'#taskgroup'.$context['task_group_id'], 'active' => false],
            ['label' => 'Edit Task', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/task_groups/form.php';
    }

    public function reorder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $order = $data['order'] ?? [];

        foreach ($order as $position => $id) {
            $stmt = $this->pdo->prepare("UPDATE task_groups SET position = ? WHERE id = ?");
            $stmt->execute([$position, $id]);
        }

        http_response_code(204);
    }

    public function update()
    {
        $data = $_POST;
        $testId = $data['test_id'] ?? 0;
        $taskGroupId = $data['id'] ?? 0;
        $stmt = $this->pdo->prepare("UPDATE task_groups SET title = ?, position = ? WHERE id = ?");
        $stmt->execute(
            [
            $data['title'],
            $data['position'],
            $data['id']
            ]
        );

        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#taskgroup".$taskGroupId);
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $this->pdo->prepare("SELECT test_id FROM task_groups WHERE id = ?");
        $stmt->execute([$id]);
        $testId = $stmt->fetchColumn();
        $taskGroupId = $id;
        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM task_groups WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#task-group-list");
        exit;
    }
}
