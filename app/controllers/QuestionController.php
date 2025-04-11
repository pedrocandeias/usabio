<?php

class QuestionController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        $this->pdo = $pdo;
    }

    private function userCanAccessGroup($groupId)
    {
        if ($_SESSION['is_admin']) return true;

        $stmt = $this->pdo->prepare("
            SELECT 1 FROM project_user pu
            JOIN projects p ON pu.project_id = p.id
            JOIN tests t ON t.project_id = p.id
            JOIN questionnaire_groups qg ON qg.test_id = t.id
            WHERE qg.id = ? AND pu.moderator_id = ?
        ");
        $stmt->execute([$groupId, $_SESSION['user_id']]);
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $groupId = $_GET['group_id'] ?? 0;

        if (!$this->userCanAccessGroup($groupId)) {
            echo "Access denied.";
            exit;
        }

        $question = [
            'id' => 0,
            'questionnaire_group_id' => $groupId,
            'text' => '',
            'question_type' => 'text',
            'question_options' => '',
            'position' => 0
        ];

        require __DIR__ . '/../views/questions/form.php';
    }

    public function store()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare("
            INSERT INTO questions (questionnaire_group_id, text, question_type, question_options, position)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['questionnaire_group_id'],
            $data['text'],
            $data['question_type'],
            $data['question_options'],
            $data['position'] ?? 0
        ]);

        header("Location: /index.php?controller=Test&action=show&id=" . $_POST['test_id']);
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question || !$this->userCanAccessGroup($question['questionnaire_group_id'])) {
            echo "Access denied or not found.";
            exit;
        }

        require __DIR__ . '/../views/questions/form.php';
    }

    public function update()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare("
            UPDATE questions SET
                text = ?, question_type = ?, question_options = ?, position = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['text'],
            $data['question_type'],
            $data['question_options'],
            $data['position'],
            $data['id']
        ]);

        header("Location: /index.php?controller=Test&action=show&id=" . $data['test_id']);
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
        $stmt = $this->pdo->prepare("
            SELECT t.id FROM tests t
            JOIN questionnaire_groups qg ON qg.test_id = t.id
            WHERE qg.id = ?
        ");
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
    
}
