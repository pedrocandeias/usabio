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
        require __DIR__ . '/../views/projects/index.php';
    }

    /**
     * Show create project form
     */
    public function create()
    {
        // Default empty project fields
        $project = [
            'id' => 0,
            'product_under_test' => '',
            'business_case' => '',
            'test_objectives' => '',
            'participants' => '',
            'equipment' => '',
            'responsibilities' => '',
            'location_dates' => '',
            'test_procedure' => '',
        ];

        require __DIR__ . '/../views/projects/form.php';
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

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $project = $this->projectModel->find($id);

        if (!$project) {
            echo "Project not found.";
            exit;
        }

        require __DIR__ . '/../views/projects/form.php';
    }

    /**
     * Update an existing project
     */
    public function update()
    {
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
        $id = $_GET['id'] ?? 0;
        $this->projectModel->delete($id);

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }
}
