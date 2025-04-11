<?php

class TestController
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

    public function index()
    {
        $projectId = $_GET['project_id'] ?? 0;

        if (!$this->userCanAccessProject($projectId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$projectId]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/tests/index.php';
    }

    public function create()
    {
        $projectId = $_GET['project_id'] ?? 0;

        if (!$this->userCanAccessProject($projectId)) {
            echo "Access denied.";
            exit;
        }

        $test = [
            'id' => 0,
            'title' => '',
            'description' => '',
            'layout_image' => '',
            'project_id' => $projectId
        ];

        include __DIR__ . '/../views/tests/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }
    
        $data = $_POST;
    
        // Handle uploaded image
        $layoutImageName = '';
        if (!empty($_FILES['layout_image']['name'])) {
            $layoutImageName = uniqid() . '_' . basename($_FILES['layout_image']['name']);
            $targetPath = __DIR__ . '/../../uploads/' . $layoutImageName;
            move_uploaded_file($_FILES['layout_image']['tmp_name'], $targetPath);
        }
    
        // Insert into database
        $stmt = $this->pdo->prepare(
            "
            INSERT INTO tests (project_id, title, description, layout_image)
            VALUES (:project_id, :title, :description, :layout_image)
        "
        );
        $stmt->execute(
            [
            ':project_id' => $data['project_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':layout_image' => $layoutImageName
            ]
        );
    
        // Redirect to the project's detail page
        header("Location: /index.php?controller=Project&action=show&id=" . $data['project_id']);
        exit;
    }
    

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test || !$this->userCanAccessProject($test['project_id'])) {
            echo "Access denied or test not found.";
            exit;
        }

        include __DIR__ . '/../views/tests/form.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }

        $data = $_POST;
        $testId = $data['id'];
        $projectId = $data['project_id'];

        // Use existing image if no new file uploaded
        $layoutImageName = $data['existing_layout_image'] ?? '';

        // Handle new image upload if one was provided
        if (!empty($_FILES['layout_image']['name'])) {
            $layoutImageName = uniqid() . '_' . basename($_FILES['layout_image']['name']);
            $targetPath = __DIR__ . '/../../uploads/' . $layoutImageName;
        
            if (!move_uploaded_file($_FILES['layout_image']['tmp_name'], $targetPath)) {
                echo "⚠️ Failed to upload file. Temp file: " . $_FILES['layout_image']['tmp_name'];
                exit;
            }
        }

        // Update the database
        $stmt = $this->pdo->prepare(
            "
        UPDATE tests SET
            title = :title,
            description = :description,
            layout_image = :layout_image
        WHERE id = :id
    "
        );
        $stmt->execute(
            [
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':layout_image' => $layoutImageName,
            ':id' => $testId
            ]
        );

        // Redirect back to project detail
        header("Location: /index.php?controller=Project&action=show&id=" . $projectId);
        exit;
    }
    
    public function show()
    {
        $testId = $_GET['id'] ?? 0;

        if (!$testId) {
            echo "Missing test ID.";
            exit;
        }

        // Fetch the test
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$testId]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            echo "Test not found.";
            exit;
        }

        
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


        
        // Access control: user must be admin or assigned to the project
        $projectId = $test['project_id'];
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // === FETCH TASK GROUPS + TASKS ===
        $stmt = $this->pdo->prepare(
            "
        SELECT 
            g.id AS group_id, g.title AS group_title, g.position AS group_position,
            t.id AS task_id, t.task_text, t.task_type, t.position AS task_position
        FROM task_groups g
        LEFT JOIN tasks t ON g.id = t.task_group_id
        WHERE g.test_id = ?
        ORDER BY g.position, g.id, t.position
    "
        );
        $stmt->execute([$testId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Organize data: group ID → group + tasks[]
        $taskGroups = [];

        foreach ($rows as $row) {
            $groupId = $row['group_id'];

            if (!isset($taskGroups[$groupId])) {
                $taskGroups[$groupId] = [
                'id' => $groupId,
                'title' => $row['group_title'],
                'position' => $row['group_position'],
                'tasks' => []
                ];
            }

            if (!empty($row['task_id'])) {
                $taskGroups[$groupId]['tasks'][] = [
                'id' => $row['task_id'],
                'task_text' => $row['task_text'],
                'task_type' => $row['task_type'],
                'position' => $row['task_position']
                ];
            }
        }

        // === FETCH QUESTIONNAIRE GROUPS ===
        $stmt = $this->pdo->prepare(
            "
        SELECT * FROM questionnaire_groups
        WHERE test_id = ?
        ORDER BY position ASC, id ASC
        "
        );
        $stmt->execute([$test['id']]);
        $questionnaireGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questionnaireGroups as &$qGroup) {
            $stmt = $this->pdo->prepare("
                SELECT id, text, question_type, position
                FROM questions
                WHERE questionnaire_group_id = ?
                ORDER BY position ASC
            ");
            $stmt->execute([$qGroup['id']]);
            $qGroup['questions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Send everything to the view
        include __DIR__ . '/../views/tests/show.php';
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT project_id FROM tests WHERE id = ?");
        $stmt->execute([$id]);
        $projectId = $stmt->fetchColumn();

        if (!$this->userCanAccessProject($projectId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM tests WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Project&action=show&id=" . $projectId);
        exit;
    }

    private function userCanAccessProject($projectId)
    {
        if ($_SESSION['is_admin']) { return true;
        }

        $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
        $stmt->execute([$projectId, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }
}
