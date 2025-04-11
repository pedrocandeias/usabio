<?php

class QuestionnaireGroupController
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

    private function userCanAccessTest($testId)
    {
        if ($_SESSION['is_admin']) return true;

        $stmt = $this->pdo->prepare("
            SELECT 1 FROM project_user pu
            JOIN projects p ON pu.project_id = p.id
            JOIN tests t ON t.project_id = p.id
            WHERE t.id = ? AND pu.moderator_id = ?
        ");
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

        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id = ? ORDER BY position ASC, id ASC");
        $stmt->execute([$testId]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/questionnaire_groups/index.php';
    }

    public function create()
    {
        $testId = $_GET['test_id'] ?? 0;

        if (!$this->userCanAccessTest($testId)) {
            echo "Access denied.";
            exit;
        }

        $group = ['id' => 0, 'test_id' => $testId, 'title' => '', 'position' => 0];

        require __DIR__ . '/../views/questionnaire_groups/form.php';
    }

    public function store()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare("INSERT INTO questionnaire_groups (test_id, title, position) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['test_id'],
            $data['title'],
            $data['position'] ?? 0
        ]);

        header("Location: /index.php?controller=QuestionnaireGroup&action=index&test_id=" . $data['test_id']);
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE id = ?");
        $stmt->execute([$id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$group || !$this->userCanAccessTest($group['test_id'])) {
            echo "Access denied or group not found.";
            exit;
        }

        require __DIR__ . '/../views/questionnaire_groups/form.php';
    }

    public function reorder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $order = $data['order'] ?? [];

        foreach ($order as $position => $id) {
            $stmt = $this->pdo->prepare("UPDATE questionnaire_groups SET position = ? WHERE id = ?");
            $stmt->execute([$position, $id]);
        }

        http_response_code(204);
    }


    public function update()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare("UPDATE questionnaire_groups SET title = ?, position = ? WHERE id = ?");
        $stmt->execute([
            $data['title'],
            $data['position'],
            $data['id']
        ]);

        header("Location: /index.php?controller=QuestionnaireGroup&action=index&test_id=" . $data['test_id']);
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

        header("Location: /index.php?controller=QuestionnaireGroup&action=index&test_id=" . $testId);
        exit;
    }
}
