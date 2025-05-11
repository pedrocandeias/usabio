<?php

class QuestionnaireGroupController
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
            SELECT 1 FROM project_user pu
            JOIN projects p ON pu.project_id = p.id
            JOIN tests t ON t.project_id = p.id
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

        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id = ? ORDER BY position ASC");
        $stmt->execute([$testId]);
        $questionnaireGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch test + project context
        $stmt = $this->pdo->prepare(
            "
        SELECT 
            t.title AS test_title, 
            p.id AS project_id,
            p.product_under_test AS project_name
        FROM tests t
        JOIN projects p ON p.id = t.project_id
        WHERE t.id = ?
    "
        );
        $stmt->execute([$testId]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/questionnaire_groups/index.php';
    }

    public function create()
    {
        $testId = $_GET['test_id'] ?? 0;

        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $group = [
            'id' => 0,
            'test_id' => $testId,
            'title' => '',
            'position' => 0
        ];
        
        // Fetch test + project context
        $stmt = $this->pdo->prepare(
            "
 SELECT 
     t.title AS test_title,
     t.id AS test_id,
     p.id AS project_id,
     p.product_under_test AS project_name
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
            ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $testId . '#questionnairegroup', 'active' => false],
            ['label' => 'Create Questionnaire Group', 'url' => '', 'active' => true],
        ];
    

        include __DIR__ . '/../views/questionnaire_groups/form.php';
    }

    public function store()
    {
        $data = $_POST;
        $testId = $data['test_id'];
        $stmt = $this->pdo->prepare("INSERT INTO questionnaire_groups (test_id, title, position) VALUES (?, ?, ?)");
        $stmt->execute(
            [
            $data['test_id'],
            $data['title'],
            $data['position'] ?? 0
            ]
        );

        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#questionnairegroup");

        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
    
        // Load the questionnaire group
        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE id = ?");
        $stmt->execute([$id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$group || !$this->userCanAccessTest($group['test_id'])) {
            echo "Access denied or group not found.";
            exit;
        }
    
        $testId = $group['test_id'];
    
        // Load test + project context
        $stmt = $this->pdo->prepare("
            SELECT 
                t.title AS test_title, 
                t.id AS test_id,
                p.id AS project_id,
                p.product_under_test AS project_name
            FROM tests t
            JOIN projects p ON p.id = t.project_id
            WHERE t.id = ?
        ");
        $stmt->execute([$testId]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Add breadcrumbs with anchor to this specific group
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
            ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $testId . '#questionnaire-group' . $id, 'active' => false],
            ['label' => 'Edit Questionnaire Group', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/questionnaire_groups/form.php';
    }
    

   public function reorder() {
    if (!isset($_SESSION['username'])) {
        http_response_code(403);
        exit('Not authorized');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $order = $data['order'] ?? [];

    if (!is_array($order)) {
        http_response_code(400);
        exit('Invalid data');
    }

    foreach ($order as $position => $id) {
        $stmt = $this->pdo->prepare("UPDATE questionnaire_groups SET position = ? WHERE id = ?");
        $stmt->execute([$position, $id]);
    }

    http_response_code(204);
}

    public function update()
    {
        $data = $_POST;  
        $testId = $data['test_id'];
        $stmt = $this->pdo->prepare("UPDATE questionnaire_groups SET title = ?, position = ? WHERE id = ?");
        $stmt->execute(
            [
            $data['title'],
            $data['position'],
            $data['id']
            ]
        );
        $qID = $data['id'];


        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#questionnaire-group".$qID);
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT test_id FROM questionnaire_groups WHERE id = ?");
        $stmt->execute([$id]);
        $testId = $stmt->fetchColumn();

        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM questionnaire_groups WHERE id = ?");
        $stmt->execute([$id]);
       
        header("Location: /index.php?controller=Test&action=show&id=" . $testId."#questionnairegroup");
        exit;
    }
}
