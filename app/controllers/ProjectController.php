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
    
        // Create the project and get its ID
        $projectId = $this->projectModel->create($data);
    
        // Assign users if provided
        $assignedUsers = $_POST['assigned_users'] ?? [];
        if (!empty($assignedUsers)) {
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $userId) {
                $stmt->execute([$projectId, $userId]);
            }
        }
    
        // Redirect to project detail view
        header("Location: /index.php?controller=Project&action=show&id=" . $projectId);
        exit;
    }
    

    public function show()
    {
        $projectId = $_GET['id'] ?? 0;

        if (!$projectId) {
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
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();

            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }

        // Fetch project
        $project = $this->projectModel->find($projectId);
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
        $stmt->execute([$projectId]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch related tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);
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
        $stmt->execute([$projectId]);
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
        $stmt->execute([$projectId]);
        $testAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group tests per participant
        $testsByParticipant = [];
        foreach ($testAssignments as $row) {
            $testsByParticipant[$row['participant_id']][] = $row['test_title'];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$projectId]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get test titles for quick lookup
        $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);

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

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/projects/show.php';
    }


    /**
     * Show edit form
     */
    public function edit()
    {
        $projectId = $_GET['id'] ?? 0;
    
        if (!$projectId) {
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
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
    
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
    
        $project = $this->projectModel->find($projectId);
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        // Load all moderators for assignment UI
        $stmt = $this->pdo->query("SELECT id, username FROM moderators ORDER BY username");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch currently assigned users
        $stmt = $this->pdo->prepare("SELECT moderator_id FROM project_user WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $assignedUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        
        include __DIR__ . '/../views/projects/form.php';
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
    
        $projectId = $_POST['id'] ?? 0;
    
        if (!$projectId) {
            echo "Missing project ID.";
            exit;
        }
    
        // Access control
        if (!($_SESSION['is_admin'] ?? false) && !($_SESSION['is_superadmin'] ?? false)) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            $authorized = $stmt->fetchColumn();
    
            if (!$authorized) {
                echo "Access denied: You are not assigned to this project.";
                exit;
            }
        }
    
        // Update project data
        $data = $_POST;
        $this->projectModel->update($projectId, $data);
    
        // Update assigned users
        $assignedUsers = $_POST['assigned_users'] ?? [];
    
        // Clear old assignments
        $stmt = $this->pdo->prepare("DELETE FROM project_user WHERE project_id = ?");
        $stmt->execute([$projectId]);
    
        // Insert new ones
        if (!empty($assignedUsers)) {
            $stmt = $this->pdo->prepare("INSERT INTO project_user (project_id, moderator_id) VALUES (?, ?)");
            foreach ($assignedUsers as $userId) {
                $stmt->execute([$projectId, $userId]);
            }
        }
    
        header("Location: /index.php?controller=Project&action=show&id=" . $projectId);
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
        $projectId = $_GET['id'] ?? 0;

        // Check access
        if (!$_SESSION['is_admin']) {
            $stmt = $this->pdo->prepare("SELECT 1 FROM project_user WHERE project_id = ? AND moderator_id = ?");
            $stmt->execute([$projectId, $_SESSION['user_id']]);
            if (!$stmt->fetchColumn()) {
                echo "Access denied.";
                exit;
            }
        }

        // Fetch project info
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // Fetch all test IDs in this project
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE project_id = ?");
        $stmt->execute([$projectId]);
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
        $avgTime = round($stmt->fetchColumn());

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
        $stmt->execute([$projectId]);
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
            $stmt->execute([$projectId]);
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
                $stmt->execute([$projectId]);
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

        }


        include __DIR__ . '/../views/projects/analysis.php';
    }

    public function newOptions()
    {
        include __DIR__ . '/../views/projects/new_options.php';
    }

}
