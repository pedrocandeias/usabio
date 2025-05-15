<?php
require_once __DIR__ . '/BaseController.php'; // carrega o base
require_once __DIR__ . '/../models/Project.php';

class ProjectController extends BaseController

{

    protected $projectModel;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
            exit;
        }
        parent::__construct($pdo); // Inicializa $this->pdo antes de usá-lo
        $this->projectModel = new Project($this->pdo); // usa $this->pdo já inicializado
    
    }

   /**
 * List all projects
 */
/**
 * List all projects
 */
public function index()
{
    $userId = $_SESSION['user_id'] ?? null;
    $isSuperadmin = $_SESSION['is_superadmin'] ?? false;

    if (!$userId) {
        header("Location: /index.php?controller=Auth&action=login");
        exit;
    }

    $filter = $_GET['filter'] ?? null;

    if ($isSuperadmin) {
        // SUPERADMIN: always see everything
        $stmt = $this->pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($filter === 'my') {
        // USER + filter=my → owned OR assigned as admin
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT p.*
            FROM projects p
            LEFT JOIN project_user pu ON pu.project_id = p.id
            WHERE p.owner_id = :user_id OR (pu.moderator_id = :user_id AND pu.is_admin = 1)
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // USER → owned OR assigned (any role)
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT p.*
            FROM projects p
            LEFT JOIN project_user pu ON pu.project_id = p.id
            WHERE p.owner_id = :user_id OR pu.moderator_id = :user_id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $breadcrumbs = [
        ['label' => __('projects'), 'url' => '/index.php?controller=Project&action=index', 'active' => false],
    ];

    include __DIR__ . '/../views/projects/index.php';
}

    /**
     * Show create project form
     */
    public function create()
    {


        $data = $_POST;

        // Handle image upload
        $projectImageName = '';
        if (!empty($_FILES['layout_image']['name'])) {
            $projectImageName = uniqid() . '_' . basename($_FILES['project_image']['name']);
            move_uploaded_file(
                $_FILES['project_image']['tmp_name'],
                __DIR__ . '/../../uploads/' . $projectImageName
            );
        }

        $data['project_image'] = $projectImageName;

        // Default empty project fields
        $project = [
            'id' => 0,
            'title' => '',
            'description' => '',
            'status' => '',
            'product_under_test' => '',
            'business_case' => '',
            'test_objectives' => '',
            'participants' => '',
            'equipment' => '',
            'responsibilities' => '',
            'location_dates' => '',
            'test_procedure' => '',
            'project_image' => '',
        ];

        // Fetch all users to show in the multiselect
        $stmt = $this->pdo->query("SELECT id, username FROM moderators ORDER BY username");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assignedUsers = []; // for create
        
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],

        ];

        include __DIR__ . '/../views/projects/add_project.php';

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

        $ownerId = $_SESSION['user_id'] ?? null;
        if (!$ownerId) {
            echo "User not authenticated.";
            exit;
        }

         // Superadmins bypass limit
        if (empty($_SESSION['is_superadmin'])) {
            $userType = $_SESSION['user_type'] ?? 'normal';
            $limit = $this->getMaxProjectsForUserType($userType);

            // Count current projects owned by the user
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM projects WHERE owner_id = ?");
            $stmt->execute([$ownerId]);
            $projectCount = $stmt->fetchColumn();


            if ($currentCount >= $limit) {
                $_SESSION['toast_error'] = "⚠️ Reached your limit of $limit projects for your account type.";
                header('Location: /index.php?controller=Project&action=index');
                exit;
            }
            
        }

        if (!empty($_FILES['project_image']['name'])) {
            if ($_FILES['project_image']['size'] > 2 * 1024 * 1024) {
                echo __('image_size_2mb');
                exit;
            }
        
            $projectImageName = uniqid() . '_' . basename($_FILES['project_image']['name']);
            move_uploaded_file(
                $_FILES['project_image']['tmp_name'],
                __DIR__ . '/../../uploads/' . $projectImageName
            );
            $data['project_image'] = $projectImageName;
        }
    
        $data['owner_id'] = $ownerId;

        // Create the project and get its ID
        $project_id = $this->projectModel->create($data);
        
        $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
        $stmt->execute([$project_id, $ownerId]);
        // Assign users if provided
        $assignedUsers = $_POST['assigned_users'] ?? [];
        foreach ($assignedUsers as $userId) {
            // Evitar duplicação do owner
            if ($userId != $ownerId) {
                $stmt->execute([$project_id, $userId]);
            }
        }
    
        // Redirect to project detail view
        header("Location: /index.php?controller=Project&action=show&id=" . $project_id);
        exit;
    }
    

    public function show()
    {
        $this->requireProjectAccess(); // 🔐 Verifica se o user pode ver este projeto
    
        $project_id = $_GET['id'] ?? 0;
        if (!$project_id) {
            echo "Invalid project ID.";
            exit;
        }
    
        // Fetch project
        $project = $this->projectModel->find($project_id);
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        $stmt = $this->pdo->prepare(
            "
            SELECT m.id, m.username 
            FROM project_user pu 
            JOIN moderators m ON pu.moderator_id = m.id 
            WHERE pu.project_id = ?
        "
        );
        $stmt->execute([$project_id]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch related tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get participants for the project
        $stmt = $this->pdo->prepare(
            "
            SELECT p.*, t.title AS test_title
            FROM participants p
            LEFT JOIN tests t ON p.test_id = t.id
            WHERE p.project_id = ?
            ORDER BY p.created_at DESC
        "
        );
        $stmt->execute([$project_id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch assigned tests per participant
        $stmt = $this->pdo->prepare(
            "
            SELECT pt.participant_id, t.id AS test_id, t.title AS test_title
            FROM participant_test pt
            JOIN tests t ON pt.test_id = t.id
            WHERE pt.participant_id IN (
                SELECT id FROM participants WHERE project_id = ?
            )
        "
        );
        $stmt->execute([$project_id]);
        $testAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group tests per participant
        $testsByParticipant = [];
        foreach ($testAssignments as $row) {
            $testsByParticipant[$row['participant_id']][] = $row['test_title'];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get test titles for quick lookup
        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);

        $testTitleMap = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $test) {
            $testTitleMap[$test['id']] = $test['title'];
        }

        // Group completed test titles per participant
        $completedTestsByParticipant = [];

        foreach ($participants as $p) {
            $completedIds = array_filter(array_map('trim', explode(',', $p['completed_test_ids'] ?? '')));
            foreach ($completedIds as $testId) {
                if (isset($testTitleMap[$testId])) {
                    $completedTestsByParticipant[$p['id']][] = $testTitleMap[$testId];
                }
            }
        }

        // Fetch total evaluations
    $stmt = $this->pdo->prepare("
    SELECT
        SUM(CASE WHEN did_tasks = 1 THEN 1 ELSE 0 END) AS total_tasks,
        SUM(CASE WHEN did_questionnaire = 1 THEN 1 ELSE 0 END) AS total_questionnaires
    FROM evaluations
    WHERE test_id IN (
        SELECT id FROM tests WHERE project_id = ?
    )
    ");
    $stmt->execute([$project_id]);
    $evaluationTotals = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalTaskEvaluations = $evaluationTotals['total_tasks'] ?? 0;
    $totalQuestionnaireEvaluations = $evaluationTotals['total_questionnaires'] ?? 0;


    $stmt = $this->pdo->prepare("
    SELECT p.*, m.fullname AS owner_name
    FROM projects p
    JOIN moderators m ON p.owner_id = m.id
    WHERE p.id = ?");
    $stmt->execute([$project_id]);
    $owners = $stmt->fetch(PDO::FETCH_ASSOC);

   
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '', 'active' => true],
        ];

        $projectBase = $this->projectBase;
        $projectTests = $this->projectTests;
        $projectParticipants = $this->projectParticipants;
        $projectAssignedUsers = $this->projectAssignedUsers;

        include __DIR__ . '/../views/projects/show.php';
    }


    /**
     * Show edit form
     */
    public function edit()
    {
        $project_id = $_GET['id'] ?? 0;
    
        if (!$project_id) {
            echo "Invalid project ID.";
            exit;
        }
        
        // Allow superadmins and admins, restrict for regular moderators
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare(
                "
                SELECT 1 FROM project_user
                WHERE project_id = ? AND moderator_id = ?
            "
            );
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
    
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
    
        $project = $this->projectModel->find($project_id);
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        if (!$this->userIsProjectAdmin($project_id)) {
            echo "Access denied.";
            exit;
        }
        
        // Load all moderators for assignment UI
        $stmt = $this->pdo->query("SELECT id, username FROM moderators ORDER BY username");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch currently assigned users
        $stmt = $this->pdo->prepare("SELECT moderator_id FROM project_user WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '', 'active' => true],
        ];
        
        include __DIR__ . '/../views/projects/edit_project.php';
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
    
        $project_id = $_POST['id'] ?? 0;
    
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }
    

        if (!$this->userIsProjectAdmin($project_id)) {
            echo "Access denied.";
            exit;
        }
        

        // Access control
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
    
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
    
        
        // Update project data
        $data = $_POST;

        if (!empty($_FILES['project_image']['name'])) {
            if ($_FILES['project_image']['size'] > 2 * 1024 * 1024) {
                echo "Imagem demasiado grande (limite de 2MB).";
                exit;
            }
        
            $projectImageName = uniqid() . '_' . basename($_FILES['project_image']['name']);
            move_uploaded_file(
                $_FILES['project_image']['tmp_name'],
                __DIR__ . '/../../uploads/' . $projectImageName
            );
            $data['project_image'] = $projectImageName;
        }



        $this->projectModel->update($project_id, $data);
    
        header("Location: /index.php?controller=Project&action=show&id=" . $project_id);
        exit;
    }
    

    /**
     * Delete a project
     */
    public function destroy()
    {
        $id = $_GET['id'] ?? 0;
        $project_id = $id;

        if (!$_SESSION['is_admin']) {   
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
        
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        if (!$this->userIsProjectAdmin($project_id)) {
            echo "Access denied.";
            exit;
        }

        $this->projectModel->delete($id);

        header('Location: /index.php?controller=Project&action=index');
        exit;
    }

    public function newOptions()
    {
        include __DIR__ . '/../views/projects/new_options.php';
    }

}
