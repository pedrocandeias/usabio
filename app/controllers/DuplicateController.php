<?php

class DuplicateController {
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        $this->pdo = $pdo;
    }

    public function duplicateProject()
    {
        $originalId = $_GET['id'] ?? 0;

        // Step 1: Fetch original project
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$originalId]);
        $original = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$original) {
            echo "Original project not found.";
            exit;
        }

        // Step 2: Insert new project
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (title, description, product_under_test, business_case, test_objectives, participants, equipment, responsibilities, location_dates, test_procedure, project_image, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $newTitle = 'Copy of ' . $original['title'];
        $stmt->execute([
            $newTitle,
            $original['description'],
            $original['product_under_test'],
            $original['business_case'],
            $original['test_objectives'],
            $original['participants'],
            $original['equipment'],
            $original['responsibilities'],
            $original['location_dates'],
            $original['test_procedure'],
            $original['project_image'],
        ]);
        $newProjectId = $this->pdo->lastInsertId();

        // Step 3: Copy participant custom fields
        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ?");
        $stmt->execute([$originalId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $field) {
            $insert = $this->pdo->prepare("INSERT INTO participants_custom_fields (project_id, label, field_type, options, position) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([
                $newProjectId,
                $field['label'],
                $field['field_type'],
                $field['options'],
                $field['position']
            ]);
        }

        // Step 4: Copy tests
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE project_id = ?");
        $stmt->execute([$originalId]);
        $originalTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($originalTests as $test) {
            $insert = $this->pdo->prepare("INSERT INTO tests (project_id, title, description, layout_image, created_at) VALUES (?, ?, ?, ?, NOW())");
            $insert->execute([$newProjectId, $test['title'], $test['description'], $test['layout_image']]);
            $newTestId = $this->pdo->lastInsertId();

            // Copy task groups
            $tgStmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id = ?");
            $tgStmt->execute([$test['id']]);
            foreach ($tgStmt->fetchAll(PDO::FETCH_ASSOC) as $tg) {
                $insertTG = $this->pdo->prepare("INSERT INTO task_groups (test_id, title, position) VALUES (?, ?, ?)");
                $insertTG->execute([$newTestId, $tg['title'], $tg['position']]);
                $newTGId = $this->pdo->lastInsertId();

                // Copy tasks
                $taskStmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ?");
                $taskStmt->execute([$tg['id']]);
                foreach ($taskStmt->fetchAll(PDO::FETCH_ASSOC) as $task) {
                    $insertTask = $this->pdo->prepare("INSERT INTO tasks (task_group_id, task_text, preset_type, script, scenario, metrics, task_type, task_options, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertTask->execute([
                        $newTGId,
                        $task['task_text'],
                        $task['preset_type'],
                        $task['script'],
                        $task['scenario'],
                        $task['metrics'],
                        $task['task_type'],
                        $task['task_options'],
                        $task['position']
                    ]);
                }
            }

            // Copy questionnaire groups
            $qgStmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id = ?");
            $qgStmt->execute([$test['id']]);
            foreach ($qgStmt->fetchAll(PDO::FETCH_ASSOC) as $qg) {
                $insertQG = $this->pdo->prepare("INSERT INTO questionnaire_groups (test_id, title, position) VALUES (?, ?, ?)");
                $insertQG->execute([$newTestId, $qg['title'], $qg['position']]);
                $newQGId = $this->pdo->lastInsertId();

                // Copy questions
                $qStmt = $this->pdo->prepare("SELECT * FROM questions WHERE questionnaire_group_id = ?");
                $qStmt->execute([$qg['id']]);
                foreach ($qStmt->fetchAll(PDO::FETCH_ASSOC) as $q) {
                    $insertQ = $this->pdo->prepare("INSERT INTO questions (questionnaire_group_id, text, question_type, question_options, position, preset_type) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertQ->execute([
                        $newQGId,
                        $q['text'],
                        $q['question_type'],
                        $q['question_options'],
                        $q['position'],
                        $q['preset_type']
                    ]);
                }
            }
        }

        // Done! Redirect to project page
        header("Location: /index.php?controller=Project&action=show&id=" . $newProjectId);
        exit;
    }

    public function selectProject()
    {
        $stmt = $this->pdo->query("SELECT id, title FROM projects ORDER BY created_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        include __DIR__ . '/../views/duplicate/select_project.php';
    }
    
    public function duplicateTest()
    {
        $originalId = $_GET['id'] ?? 0;

        // Step 1: Fetch original test
        $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = ?");
        $stmt->execute([$originalId]);
        $original = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$original) {
            echo "Original test not found.";
            exit;
        }

        // Step 2: Insert new test
        $stmt = $this->pdo->prepare("INSERT INTO tests (project_id, title, description, layout_image, created_at) VALUES (?, ?, ?, ?, NOW())");
        $newTitle = 'Copy of ' . $original['title'];
        $stmt->execute([$original['project_id'], $newTitle, $original['description'], $original['layout_image']]);
        $newTestId = $this->pdo->lastInsertId();

        // Step 3: Copy task groups and tasks
        $stmt = $this->pdo->prepare("SELECT * FROM task_groups WHERE test_id = ?");
        $stmt->execute([$originalId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $tg) {
            $insertTG = $this->pdo->prepare("INSERT INTO task_groups (test_id, title, position) VALUES (?, ?, ?)");
            $insertTG->execute([$newTestId, $tg['title'], $tg['position']]);
            $newTGId = $this->pdo->lastInsertId();

            // Copy tasks
            $taskStmt = $this->pdo->prepare("SELECT * FROM tasks WHERE task_group_id = ?");
            $taskStmt->execute([$tg['id']]);
            foreach ($taskStmt->fetchAll(PDO::FETCH_ASSOC) as $task) {
                $insertTask = $this->pdo->prepare("INSERT INTO tasks (task_group_id, task_text, preset_type, script, scenario, metrics, task_type, task_options, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertTask->execute([
                    $newTGId,
                    $task['task_text'],
                    $task['preset_type'],
                    $task['script'],
                    $task['scenario'],
                    $task['metrics'],
                    $task['task_type'],
                    $task['task_options'],
                    $task['position']
                ]);
            }
        }

        // Step 4: Copy questionnaire groups and questions
        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire_groups WHERE test_id = ?");
        $stmt->execute([$originalId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $qg) {
            $insertQG = $this->pdo->prepare("INSERT INTO questionnaire_groups (test_id, title, position) VALUES (?, ?, ?)");
            $insertQG->execute([$newTestId, $qg['title'], $qg['position']]);
            $newQGId = $this->pdo->lastInsertId();

            // Copy questions
            $qStmt = $this->pdo->prepare("SELECT * FROM questions WHERE questionnaire_group_id = ?");
            $qStmt->execute([$qg['id']]);
            foreach ($qStmt->fetchAll(PDO::FETCH_ASSOC) as $q) {
                $insertQ = $this->pdo->prepare("INSERT INTO questions (questionnaire_group_id, text, question_type, question_options, position, preset_type) VALUES (?, ?, ?, ?, ?, ?)");
                $insertQ->execute([
                    $newQGId,
                    $q['text'],
                    $q['question_type'],
                    $q['question_options'],
                    $q['position'],
                    $q['preset_type']
                ]);
            }
        }

        // Done! Redirect to new test page
        header("Location: /index.php?controller=Test&action=show&id=" . $newTestId);
        exit;
    }
}
