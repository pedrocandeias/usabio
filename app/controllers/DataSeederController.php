<?php
require_once __DIR__ . '/BaseController.php';

class DataSeederController extends BaseController
{
    public function index()
    {
        if (!($_SESSION['is_superadmin'] ?? false)) {
            echo "Access denied.";
            exit;
        }

        // Carregar projetos e testes para escolha
        $stmt = $this->pdo->query("SELECT id, title FROM projects ORDER BY created_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $selectedProjectId = $_GET['select_project_id'] ?? null;
        $tests = [];

        if ($selectedProjectId) {
            $stmt = $this->pdo->prepare("SELECT id, title FROM tests WHERE project_id = ?");
            $stmt->execute([$selectedProjectId]);
            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        include __DIR__ . '/../views/dataseeder/index.php';
    }

    public function generate()
    {
        if (!($_SESSION['is_superadmin'] ?? false)) {
            echo "Access denied.";
            exit;
        }
    
        $testId = $_POST['test_id'] ?? null;
        $projectId = $_POST['select_project_id'] ?? null;
        $count = $_POST['count'] ?? 10;
    
        if (!$testId || !$projectId || $count < 1) {
            echo "Missing or invalid parameters.";
            echo 'test'.$testId;
            echo 'project'.$projectId;
            echo 'count'.$count;
            exit;
        }
    
        // Carrega tasks
        $stmt = $this->pdo->prepare("
            SELECT t.task_text AS label, t.task_type, t.task_options
            FROM tasks t
            JOIN task_groups tg ON tg.id = t.task_group_id
            WHERE tg.test_id = ?
        ");
        $stmt->execute([$testId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Carrega questions
        $stmt = $this->pdo->prepare("
            SELECT q.text AS label, q.question_type, q.question_options
            FROM questions q
            JOIN questionnaire_groups qg ON qg.id = q.questionnaire_group_id
            WHERE qg.test_id = ?
        ");
        $stmt->execute([$testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        for ($i = 1; $i <= $count; $i++) {
            // Criar uma avaliação
            $stmt = $this->pdo->prepare("
                INSERT INTO evaluations (test_id, timestamp, participant_name, did_tasks, did_questionnaire)
                VALUES (?, NOW(), ?, 1, 1)
            ");
            $stmt->execute([
                $testId,
                'Fake User #' . $i
            ]);
            $evaluationId = $this->pdo->lastInsertId();
    
            // Inserir respostas a tasks
            foreach ($tasks as $task) {
                $answer = $this->generateFakeAnswer($task['task_type'], $task['task_options']);
                $stmt = $this->pdo->prepare("
                    INSERT INTO responses (evaluation_id, question, answer, time_spent, type)
                    VALUES (?, ?, ?, ?, 'task')
                ");
                $stmt->execute([
                    $evaluationId,
                    $task['label'],
                    $answer,
                    rand(10, 120)
                ]);
            }
    
// Carregar os campos personalizados do projeto
$stmt = $this->pdo->prepare("SELECT id, field_type FROM participants_custom_fields WHERE project_id = ?");
$stmt->execute([$project_id]);
$customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($customFields as $field) {
    $value = match ($field['field_type']) {
        'number' => rand(18, 65),
        'select' => 'Option ' . rand(1, 3),
        default => 'Test value'
    };

    $stmt = $this->pdo->prepare("
        INSERT INTO evaluation_custom_data (evaluation_id, field_id, value)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$evaluationId, $field['id'], $value]);
}

            
            // Inserir respostas a questões
            foreach ($questions as $q) {
                $answer = $this->generateFakeAnswer($q['question_type'], $q['question_options']);
                $stmt = $this->pdo->prepare("
                    INSERT INTO responses (evaluation_id, question, answer, time_spent, type)
                    VALUES (?, ?, ?, 0, 'question')
                ");
                $stmt->execute([
                    $evaluationId,
                    $q['label'],
                    $answer
                ]);
            }
        }
    
        $_SESSION['toast_success'] = "$count fake evaluations generated successfully!";
        header("Location: /index.php?controller=DataSeeder&action=index&project_id=$projectId");
        exit;
    }
    
    private function generateFakeAnswer($type, $optionsRaw)
    {
        $options = [];
    
        if (!empty($optionsRaw)) {
            foreach (explode(';', $optionsRaw) as $opt) {
                [$label, $value] = array_pad(explode(':', $opt, 2), 2, $opt);
                $options[] = trim($value);
            }
        }
    
        switch ($type) {
            case 'radio':
            case 'dropdown':
                return $options ? $options[array_rand($options)] : 'N/A';
            case 'checkbox':
                $pick = array_rand($options, min(2, count($options)));
                return is_array($pick)
                    ? implode(',', array_map(fn($k) => $options[$k], $pick))
                    : $options[$pick];
            default:
                return 'Sample text answer';
        }
    }
    
}
