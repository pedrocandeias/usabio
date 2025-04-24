<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Test.php';

class TestController extends BaseController
{
    protected $testModel;

    public function __construct($pdo)
    {
        parent::__construct($pdo); // importante chamar o construtor do pai

        if (session_status() === PHP_SESSION_NONE) { 
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        $this->testModel = new Test($pdo);
    }

    public function index()
    {
        $project_id = $_GET['project_id'] ?? 0;
    
        // Verifica se o projeto existe e se o utilizador tem acesso
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Carregar os testes do projeto com contagens de tarefas, questões e sessões
        $stmt = $this->pdo->prepare(
            "
            SELECT 
                t.*,
                (
                    SELECT COUNT(*) 
                    FROM task_groups tg 
                    JOIN tasks tk ON tg.id = tk.task_group_id 
                    WHERE tg.test_id = t.id
                ) AS task_count,
                (
                    SELECT COUNT(*) 
                    FROM questionnaire_groups qg 
                    JOIN questions q ON qg.id = q.questionnaire_group_id 
                    WHERE qg.test_id = t.id
                ) AS question_count,
                (
                    SELECT COUNT(*) 
                    FROM evaluations e 
                    WHERE e.test_id = t.id
                ) AS session_count
            FROM tests t
            WHERE t.project_id = ?
            ORDER BY t.created_at DESC
        "
        );
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Tests', 'url' => '', 'active' => true],
        ];
        include __DIR__ . '/../views/tests/index.php';
    }
    

    public function create()
    {
        $project_id = $_GET['project_id'] ?? 0;
    
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }
    
        // Check access (admin or assigned user)
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }
    
        // Fetch project name for context
        $stmt = $this->pdo->prepare("SELECT product_under_test AS project_name FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        $context = [
            'project_id' => $project_id,
            'project_name' => $project['project_name'],
        ];
    
        $test = [
            'id' => 0,
            'project_id' => $project_id,
            'title' => '',
            'description' => '',
            'layout_image' => '',
            'status' => '',
        ];
    
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Create Test', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/tests/form.php';
    }
    

    public function store()
    {

        $project_id = $_POST['project_id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /index.php?controller=Test&action=index&project_id=" . $project_id);
            exit;
        }

        $data = $_POST;

        // Handle image upload
        $layoutImageName = '';
        if (!empty($_FILES['layout_image']['name'])) {
            $layoutImageName = uniqid() . '_' . basename($_FILES['layout_image']['name']);
            move_uploaded_file(
                $_FILES['layout_image']['tmp_name'],
                __DIR__ . '/../../uploads/' . $layoutImageName
            );
        }

        $data['layout_image'] = $layoutImageName;

        // Create test and get ID
        $test_id = $this->testModel->create(
            [
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'layout_image' => $data['layout_image'],
            'status' => $data['status'] ?? 'draft'
            ]
        );
    
        $_SESSION['toast_success'] = "Task created successfully!";
        header("Location: /index.php?controller=Test&action=index&project_id=" . $data['project_id']);
        exit;
    }


    public function edit()
    {
        $id = $_GET['id'] ?? 0;
   
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        $project_id = $test['project_id'];
        $stmt = $this->pdo->prepare(
            "
            SELECT product_under_test FROM projects WHERE id = ?
        "
        );
        $stmt->execute([$project_id]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test || !$this->userCanAccessProject($test['project_id'])) {
            echo "Access denied or test not found.";
            exit;
        }

        include __DIR__ . '/../views/tests/form.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /index.php?controller=Project&action=index&id=" . $_POST['project_id'] . "#tests-list");
            exit;
        }
      
        $data = $_POST;
        $testId = $data['id'];
        $projectId = $data['project_id'];

        // Usar imagem existente se não for enviada nova
        $layoutImageName = $data['existing_layout_image'] ?? '';

        if (!empty($_FILES['layout_image']['name'])) {
            $layoutImageName = uniqid() . '_' . basename($_FILES['layout_image']['name']);
            $targetPath = __DIR__ . '/../../uploads/' . $layoutImageName;

            if (!move_uploaded_file($_FILES['layout_image']['tmp_name'], $targetPath)) {
                echo "⚠️ Falha no upload da imagem.";
                exit;
            }
        }

        $stmt = $this->pdo->prepare(
            "
        UPDATE tests SET
            title = :title,
            description = :description,
            layout_image = :layout_image,
            status = :status
        WHERE id = :id
    "
        );

        $stmt->execute(
            [
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':layout_image' => $layoutImageName,
            ':status' => $data['status'] ?? 'not_started',
            ':id' => $testId
            ]
        );
        $_SESSION['toast_success'] = "Task updated successfully!";
        header("Location: /index.php?controller=Project&action=show&id=" . $projectId . "#tests-list");
        exit;
    }

    public function duplicate()
    {
        $originalTestId = $_GET['id'] ?? 0;
    
        // Obter os dados do teste original
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$originalTestId]);
        $original = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$original) {
            echo "Original test not found.";
            exit;
        }
    
        $project_id = $original['project_id'];
    
        // Duplicar o teste
        $stmt = $this->pdo->prepare("INSERT INTO tests (project_id, title, description, layout_image, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute(
            [
            $original['project_id'],
            $original['title'] . ' (Copy)',
            $original['description'],
            $original['layout_image']
            ]
        );
    
        $newTestId = $this->pdo->lastInsertId();
    
        // Duplicar os grupos de tarefas e tarefas
        $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id = ?");
        $stmt->execute([$originalTestId]);
        $taskGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($taskGroups as $group) {
            $stmt = $this->pdo->prepare("INSERT INTO task_groups (test_id, title, position) VALUES (?, ?, ?)");
            $stmt->execute([$newTestId, $group['title'], $group['position']]);
            $newGroupId = $this->pdo->lastInsertId();
    
            $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ?");
            $stmt->execute([$group['id']]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($tasks as $task) {
                $stmt = $this->pdo->prepare("INSERT INTO tasks (task_group_id, task_text, preset_type, script, scenario, metrics, task_type, task_options, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(
                    [
                    $newGroupId,
                    $task['task_text'],
                    $task['preset_type'],
                    $task['script'],
                    $task['scenario'],
                    $task['metrics'],
                    $task['task_type'],
                    $task['task_options'],
                    $task['position']
                    ]
                );
            }
        }
    
        $_SESSION['toast_success'] = "Task duplicated successfully!";
        header("Location: /index.php?controller=Test&action=index&project_id=" . $project_id);
        exit;
    }
    
    
    public function show()
    {
        
        $test_id = $_GET['id'] ?? 0;

        if (!$test_id) {
            echo "Missing test ID.";
            exit;
        }

        // Fetch the test
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$test_id]);
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
        $stmt->execute([$test_id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        $context = [
            'test_id' => $test['id'],
            'test_title' => $test['title'],
            'project_id' => $test['project_id'],
            'product_under_test' => $test['project_name'],
            'project_name' => $test['project_name'],
        ];


        // Access control: user must be admin or assigned to the project
        $project_id = $test['project_id'];
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
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
            t.id AS task_id, t.task_text, t.task_type, t.scenario, t.script, t.metrics, t.task_options, t.position
        FROM task_groups g
        LEFT JOIN tasks t ON g.id = t.task_group_id
        WHERE g.test_id = ?
        ORDER BY g.position, g.id, t.position
    "
        );
        $stmt->execute([$test_id]);
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
                'scenario' => $row['scenario'],
                'script' => $row['script'],
                'metrics' => $row['metrics'],
                'task_options' => $row['task_options'],
                'position' => $row['position']
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
            $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE questionnaire_group_id = ? ORDER BY position ASC");
            $stmt->execute([$qGroup['id']]);
            $qGroup['questions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($qGroup); // ✅ Break reference after loop
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
            ['label' => $test['title'], 'url' => '', 'active' => true],
        ];        


          // Build breadcrumbs
          $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
            ['label' => $context['test_title'], 'url' => '', 'active' => true],
          ];

          include __DIR__ . '/../views/tests/show.php';
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT project_id FROM tests WHERE id = ?");
        $stmt->execute([$id]);
        $project_id = $stmt->fetchColumn();

        if (!$this->userCanAccessProject($project_id)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM tests WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['toast_success'] = "Task removed successfully!";
        header("Location: /index.php?controller=Test&action=index&project_id=" . $project_id);
        exit;
    }

    public function toggleStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Method not allowed";
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $testId = $data['id'] ?? null;
    $newStatus = $data['status'] ?? 'incomplete';

    if (!$testId || !in_array($newStatus, ['complete', 'incomplete'])) {
        http_response_code(400);
        echo "Invalid data.";
        exit;
    }

    $stmt = $this->pdo->prepare("UPDATE tests SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $testId]);

    echo json_encode(['success' => true]);
    exit;
}

    private function userCanAccessProject($project_id)
    {
        if ($_SESSION['is_admin']) { 
            return true;
        }

        $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
        $stmt->execute([$project_id, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }
}
