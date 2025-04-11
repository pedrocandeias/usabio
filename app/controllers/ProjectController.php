<?php

require_once __DIR__ . '/../models/Project.php';

class ProjectController
{
    private $pdo;
    private $projectModel;

    public function __construct($pdo)
    {
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect to login if not authenticated
        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
            exit;
        }

        $this->pdo = $pdo;
        $this->projectModel = new Project($pdo);
    }

    /**
     * List all projects
     */
    public function index()
    {
        $projects = $this->projectModel->all();
        include __DIR__ . '/../views/projects/index.php';
    }

    /**
     * Show create project form
     */
    public function create()
    {
        // Default empty project fields
        $project = [
            'id' => 0,
            'title' => '',
            'description' => '',
            'product_under_test' => '',
            'business_case' => '',
            'test_objectives' => '',
            'participants' => '',
            'equipment' => '',
            'responsibilities' => '',
            'location_dates' => '',
            'test_procedure' => '',
        ];

        // Fetch all users to show in the multiselect
        $stmt = $this->pdo->query("SELECT id, username FROM moderators ORDER BY username");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assignedUsers = []; // for create
        
        include __DIR__ . '/../views/projects/form.php';

    }

    /**
     * Store a new project (handle POST)
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }

        $data = $_POST;

        $this->projectModel->create($data);

        $projectId = $this->pdo->lastInsertId(); // or use $_POST['id'] in update
        $assignedUsers = $_POST['assigned_users'] ?? [];

        $stmt = $this->pdo->prepare("DELETE FROM project_user WHERE project_id = ?");
        $stmt->execute([$projectId]);

        $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
        foreach ($assignedUsers as $userId) {
            $stmt->execute([$projectId, $userId]);
        }

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }

    public function show()
    {

        
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
        
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo "Invalid project ID.";
            exit;
        }

        $project = $this->projectModel->find($id);

        if (!$project) {
            echo "Project not found.";
            exit;
        }

        $stmt = $this->pdo->prepare("
            SELECT m.id, m.username 
            FROM project_user pu 
            JOIN moderators m ON pu.moderator_id = m.id 
            WHERE pu.project_id = ?
        ");
        $stmt->execute([$id]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch related tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/projects/show.php';
    }


    /**
     * Show edit form
     */
    public function edit()
    {
        if (!$_SESSION['is_admin']) {
            $id = $_GET['id'] ?? 0;
            $project_id = $id;
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
        
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
        $id = $_GET['id'] ?? 0;
        $project = $this->projectModel->find($id);



        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // for edit
        // Fetch all users to show in the multiselect
        $stmt = $this->pdo->query("SELECT id, username FROM moderators ORDER BY username");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assignedUsers = []; // for create
        include __DIR__ . '/../views/projects/form.php';
    }

    /**
     * Update an existing project
     */
    public function update()
    {

        if (!$_SESSION['is_admin']) {
            $id = $_GET['id'] ?? 0;
            $project_id = $id;
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();

            $projectId = $this->pdo->lastInsertId(); // or use $_POST['id'] in update
            $assignedUsers = $_POST['assigned_users'] ?? [];
            
            $stmt = $this->pdo->prepare("DELETE FROM project_user WHERE project_id = ?");
            $stmt->execute([$projectId]);
            
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $userId) {
                $stmt->execute([$projectId, $userId]);
            }

            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=Project&action=index');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $data = $_POST;

        $this->projectModel->update($id, $data);

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }

    /**
     * Delete a project
     */
    public function destroy()
    {
        if (!$_SESSION['is_admin']) {
            $id = $_GET['id'] ?? 0;
            $project_id = $id;
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
        
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
        $id = $_GET['id'] ?? 0;
        $this->projectModel->delete($id);

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }
}
