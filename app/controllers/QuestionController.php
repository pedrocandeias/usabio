<?php

class QuestionController
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
            SELECT 1 FROM project_user pu
            JOIN projects p ON pu.project_id = p.id
            JOIN tests t ON t.project_id = p.id
            JOIN questionnaire_groups qg ON qg.test_id = t.id
            WHERE qg.id = ? AND pu.moderator_id = ?
        "
        );
        $stmt->execute([$groupId, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $testId = $_GET['test_id'] ?? null;
        $groupId = $_GET['group_id'] ?? null;
    
        // Create default question
        $question = [
            'id' => 0,
            'questionnaire_group_id' => $groupId,
            'text' => '',
            'question_type' => 'text',
            'question_options' => '',
            'preset_type' => '',
            'position' => 0
        ];
    
         // Load group + test context
        $stmt = $this->pdo->prepare(
            "
            SELECT qg.test_id, t.title AS test_title, p.id AS project_id, p.product_under_test AS project_name
            FROM questionnaire_groups qg
            JOIN tests t ON t.id = qg.test_id
            JOIN projects p ON p.id = t.project_id
            WHERE qg.id = ?
        "
        );
        $stmt->execute([$groupId]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
            ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'] . '#questionnaire-group-list', 'active' => false],
            ['label' => 'Create Question', 'url' => '', 'active' => true],
        ];

        if (!$context || !$this->userCanAccessGroup($question['questionnaire_group_id'])) {
            echo "Access denied or not found.";
            exit;
        }

        include __DIR__ . '/../views/questions/form.php';
    }

    public function store()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare(
            "
            INSERT INTO questions (questionnaire_group_id, text, question_type, question_options, position)
            VALUES (?, ?, ?, ?, ?)
        "
        );
        $stmt->execute(
            [
            $data['questionnaire_group_id'],
            $data['text'],
            $data['question_type'],
            $data['question_options'],
            $data['position'] ?? 0
            ]
        );

        header("Location: /index.php?controller=Test&action=show&id=" . $_POST['test_id']);
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $testId = $_GET['test_id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$question) {
            echo "Question not found.";
            exit;
        }
        if (!$question || !$this->userCanAccessGroup($question['questionnaire_group_id'])) {
            echo "Access denied or not found.";
            exit;
        }

        $stmt = $this->pdo->prepare(
            "
        SELECT qg.test_id, qg.title AS questionnaire_group_title, t.title AS test_title, p.id AS project_id, p.product_under_test AS project_name
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        JOIN projects p ON p.id = t.project_id
        WHERE qg.id = ?
    "
        );
        $stmt->execute([$question['questionnaire_group_id']]);
        $context = $stmt->fetch(PDO::FETCH_ASSOC);


        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $context['project_name'], 'url' => '/index.php?controller=Project&action=show&id=' . $context['project_id'], 'active' => false],
        ['label' => $context['test_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'] . '#questionnaire-group' . $question['questionnaire_group_id'], 'active' => false],
        ['label' => $context['questionnaire_group_title'], 'url' => '/index.php?controller=Test&action=show&id=' . $context['test_id'] . '#questionnaire-group' . $question['questionnaire_group_id'], 'active' => false],
        ['label' => 'Edit Question', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/questions/form.php';
    }


    public function update()
    {
        $data = $_POST;
        $stmt = $this->pdo->prepare("SELECT questionnaire_group_id FROM questions WHERE id = ?");
        $stmt->execute([$data['id']]);
        $groupId = $stmt->fetchColumn();
        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }
        $stmt = $this->pdo->prepare(
            "
            UPDATE questions SET
                text = ?, question_type = ?, question_options = ?, position = ?
            WHERE id = ?
        "
        );
        $stmt->execute(
            [
            $data['text'],
            $data['question_type'],
            $data['question_options'],
            $data['position'],
            $data['id']
            ]
        );

        header("Location: /index.php?controller=Test&action=show&id=" . $data['test_id'].'#questionnaire-group' . $groupId);
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT questionnaire_group_id FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $groupId = $stmt->fetchColumn();

        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$id]);

        // Get test ID for redirect
        $stmt = $this->pdo->prepare(
            "
            SELECT t.id FROM tests t
            JOIN questionnaire_groups qg ON qg.test_id = t.id
            WHERE qg.id = ?
        "
        );
        $stmt->execute([$groupId]);
        $testId = $stmt->fetchColumn();

        header("Location: /index.php?controller=Test&action=show&id=" . $testId);
        exit;
    }

    public function reorder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $groupId = $data['group_id'] ?? 0;
        $order = $data['order'] ?? [];
    
        if (!$this->userCanAccessGroup($groupId)) {
            http_response_code(403);
            echo "Access denied.";
            exit;
        }
    
        foreach ($order as $position => $questionId) {
            $stmt = $this->pdo->prepare("UPDATE questions SET position = ? WHERE id = ?");
            $stmt->execute([$position, $questionId]);
        }
    
        http_response_code(204);
    }

    public function generateSUS()
    {
            $groupId = $_GET['group_id'] ?? 0;

        if (!$groupId) {
            echo "Missing group ID.";
            exit;
        }

            // Check how many questions already exist
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM questions WHERE questionnaire_group_id = ?");
            $stmt->execute([$groupId]);
            $existing = $stmt->fetchColumn();

        if ($existing >= 10) {
            echo "This group already has 10 or more questions.";
            exit;
        }

            $questions = [
                "I think that I would like to use this system frequently.",
                "I found the system unnecessarily complex.",
                "I thought the system was easy to use.",
                "I think that I would need the support of a technical person to be able to use this system.",
                "I found the various functions in this system were well integrated.",
                "I thought there was too much inconsistency in this system.",
                "I would imagine that most people would learn to use this system very quickly.",
                "I found the system very cumbersome to use.",
                "I felt very confident using the system.",
                "I needed to learn a lot of things before I could get going with this system.",
            ];

            $preset = "Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5";

            $stmt = $this->pdo->prepare(
                "
                INSERT INTO questions (questionnaire_group_id, text, question_type, question_options, preset_type, position)
                VALUES (?, ?, 'radio', ?, 'SUS', ?)
            "
            );

        foreach ($questions as $i => $text) {
            $stmt->execute([$groupId, $text, $preset, $i + 1]);
        }

            header("Location: /index.php?controller=Test&action=show&id=" . ($_GET['test_id'] ?? ''));
            exit;
    }

    
}
