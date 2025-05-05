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
    public function index()
    {
        $breadcrumbs = [
            ['label' => __('my_projects'), 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ];

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
            'image' => '',
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
    
        // Create the project and get its ID
        $project_id = $this->projectModel->create($data);
    
        // Assign users if provided
        $assignedUsers = $_POST['assigned_users'] ?? [];
        if (!empty($assignedUsers)) {
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $user_id) {
                $stmt->execute([$project_id, $user_id]);
            }
        }
    
        // Redirect to project detail view
        header("Location: /index.php?controller=Project&action=show&id=" . $project_id);
        exit;
    }
    

    public function show()
    {
        $project_id = $_GET['id'] ?? 0;

        if (!$project_id) {
            echo "Invalid project ID.";
            exit;
        }

        // Superadmins can always access
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
        $this->projectModel->update($project_id, $data);
    
        // Update assigned users
        $assignedUsers = $_POST['assigned_users'] ?? [];
    
        // Clear old assignments
        $stmt = $this->pdo->prepare("DELETE FROM project_user WHERE project_id = ?");
        $stmt->execute([$project_id]);
    
        // Insert new ones
        if (!empty($assignedUsers)) {
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $user_id) {
                $stmt->execute([$project_id, $user_id]);
            }
        }

        $data = $_POST;
        // Usar imagem existente se não for enviada nova
        $projectImageName = $data['existing_project_image'] ?? '';

        if (!empty($_FILES['project_image']['name'])) {
            $projectImageName = uniqid() . '_' . basename($_FILES['project_image']['name']);
            $targetPath = __DIR__ . '/../../uploads/' . $projectImageName;

            if (!move_uploaded_file($_FILES['project_image']['tmp_name'], $targetPath)) {
                echo "⚠️ Falha no upload da imagem.";
                exit;
            }
        }

    
        header("Location: /index.php?controller=Project&action=show&id=" . $project_id);
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

    public function analysis()
    {
        $project_id = $_GET['id'] ?? 0;

        // Check access
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$project_id, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Fetch project info
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // Fetch all test IDs in this project
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $testIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');

        $testIdList = $testIds ? implode(',', $testIds) : '0';

        // Aggregate stats
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM evaluations WHERE test_id IN ($testIdList)");
        $totalEvaluations = $stmt->fetchColumn();

        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM responses WHERE evaluation_id IN (
        SELECT id FROM evaluations WHERE test_id IN ($testIdList)
    )"
        );
        $totalResponses = $stmt->fetchColumn();

        $stmt = $this->pdo->query(
            "SELECT AVG(time_spent) FROM responses WHERE time_spent > 0 AND evaluation_id IN (
        SELECT id FROM evaluations WHERE test_id IN ($testIdList)
    )"
        );
        $avgRaw = $stmt->fetchColumn();
        $avgTime = $avgRaw !== null ? round($avgRaw) : 0;

        $stmt = $this->pdo->query(
            "
        SELECT question_type, COUNT(*) as count FROM questions
        WHERE questionnaire_group_id IN (
            SELECT id FROM questionnaire_groups WHERE test_id IN ($testIdList)
        )
        GROUP BY question_type
    "
        );
        $questionTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // SUS Analysis (optional)
        $susScores = [];
        $susBreakdown = [];
        $susChartLabels = [];
        $susChartScores = [];


        // Get all questionnaire groups for this project
        $stmt = $this->pdo->prepare(
            "
                SELECT qg.id AS group_id
                FROM questionnaire_groups qg
                JOIN tests t ON t.id = qg.test_id
                WHERE t.project_id = ?
            "
        );
        $stmt->execute([$project_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groups as $group) {
            $susScores = [];
            $susBreakdown = [];
            $susChartLabels = [];
            $susChartScores = [];

            // Get all questionnaire groups in this project
            $stmt = $this->pdo->prepare(
                "
                    SELECT qg.id AS group_id
                    FROM questionnaire_groups qg
                    JOIN tests t ON t.id = qg.test_id
                    WHERE t.project_id = ?
                "
            );
            $stmt->execute([$project_id]);
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($groups as $group) {
                $groupId = $group['group_id'];

                // Fetch SUS questions
                $stmt = $this->pdo->prepare(
                    "
                SELECT id, text FROM questions
                WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
                ORDER BY position ASC
    "
                );
                $stmt->execute([$groupId]);
                $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($susQuestions) !== 10) { continue;
                }

                // Fetch all evaluations and responses for this project
                $stmt = $this->pdo->prepare(
                    "
                SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer
                FROM evaluations e
                JOIN responses r ON r.evaluation_id = e.id
                WHERE e.test_id IN (SELECT id FROM tests WHERE project_id = ?)
    "
                );
                $stmt->execute([$project_id]);
                $allResponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Group responses by evaluation
                $byEval = [];
                foreach ($allResponses as $resp) {
                    $byEval[$resp['evaluation_id']]['participant'] = $resp['participant_name'] ?: 'Anonymous';
                    $byEval[$resp['evaluation_id']]['answers'][$resp['question']] = (int) $resp['answer'];
                }

                foreach ($byEval as $evalId => $entry) {
                    $participant = $entry['participant'];
                    $answers = $entry['answers'];

                    $score = 0;
                    $valid = true;
                    $individualAnswers = [];

                    foreach ($susQuestions as $i => $q) {
                        $qText = $q['text'];
                        $answer = $answers[$qText] ?? null;

                        if ($answer === null || $answer < 1 || $answer > 5) {
                            $valid = false;
                            break;
                        }

                        $individualAnswers[] = $answer;
                        $score += ($i % 2 === 0) ? ($answer - 1) : (5 - $answer);
                    }

                    if ($valid) {
                        $sus = $score * 2.5;
                        $susScores[] = [
                            'participant' => $participant,
                            'score' => $sus
                        ];

                        $susBreakdown[] = [
                            'participant' => $participant,
                            'answers' => $individualAnswers,
                            'score' => $sus,
                            'label' => $sus >= 85 ? 'Excellent' : ($sus >= 70 ? 'Good' : ($sus >= 50 ? 'OK' : 'Poor'))
                        ];

                        $susChartLabels[] = $participant;
                        $susChartScores[] = $sus;
                    }
                }
            }

            // ✅ Calculate overall SUS summary once
            $susSummary = null;
            if (!empty($susBreakdown)) {
                $scores = array_column($susBreakdown, 'score');
                $avg = round(array_sum($scores) / count($scores), 1);
                $min = min($scores);
                $max = max($scores);
                $variation = ($max - $min) >= 30 ? 'high' : (($max - $min) >= 15 ? 'moderate' : 'low');
                $label = $avg >= 85 ? 'Excellent' : ($avg >= 70 ? 'Good' : ($avg >= 50 ? 'OK' : 'Poor'));
                $lowScores = count(array_filter($scores, fn($s) => $s < 50));

                $susSummary = [
                    'average' => $avg,
                    'label' => $label,
                    'low' => $lowScores,
                    'variation' => $variation
                ];
            }
            // TASK ANALYSIS
            $stmt = $this->pdo->prepare("
                SELECT r.question AS task_text, COUNT(*) AS total_responses,
                    AVG(r.time_spent) AS avg_time,
                    SUM(CASE WHEN r.evaluation_errors IS NOT NULL AND r.evaluation_errors != '' THEN 1 ELSE 0 END) AS error_count
                FROM responses r
                JOIN evaluations e ON e.id = r.evaluation_id
                WHERE r.type = 'task' AND e.test_id IN ($testIdList)
                GROUP BY r.question
                ORDER BY total_responses DESC
            ");
            $stmt->execute();
            $taskStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($taskStats as &$t) {
                $errors = $t['error_count'] ?? 0;
                $total = $t['total_responses'] ?? 1; // evitar divisão por zero
                $t['success_rate'] = round((($total - $errors) / $total) * 100, 1);
            }
            unset($t);
            


        }
        
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=edit&id='.$project_id, 'active' => false],
            ['label' => 'Analysis', 'url' => '', 'active' => true],
        ];


        include __DIR__ . '/../views/projects/analysis.php';
    }

    public function newOptions()
    {
        include __DIR__ . '/../views/projects/new_options.php';
    }

}
