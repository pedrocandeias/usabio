<?php
require_once __DIR__ . '/../helpers/openai.php';
require_once __DIR__ . '/../helpers/settings.php'; // needed for getOpenAIKey()

class ImportController
{
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

    public function uploadForm()
    {
        $project_id = $_GET['project_id'] ?? null;

        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            echo "Project not found.";
            exit;
        }

        // Load task groups for this project
        $stmt = $this->pdo->prepare(
            "
        SELECT tg.id, tg.title, t.title AS test_title
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        WHERE t.project_id = ?
        ORDER BY t.title, tg.title
    "
        );
        $stmt->execute([$project_id]);
        $taskGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load questionnaire groups for this project
        $stmt = $this->pdo->prepare(
            "
        SELECT qg.id, qg.title, t.title AS test_title
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
        ORDER BY t.title, qg.title
    "
        );
        $stmt->execute([$project_id]);
        $questionnaireGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
        ['label' => 'Import', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/import/upload.php';
    }

    public function downloadExampleProject()
    {
        $template = $_GET['template'] ?? 'lamp';

        $example = $this->loadProjectTemplate($template);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="example_project_import.json"');
        echo json_encode($example, JSON_PRETTY_PRINT);
        exit;
    }


    private function loadProjectTemplate(string $name): array
    {
        $templates = [
        'lamp' => [
            'project' => [
                'title' => 'Smart Lamp Usability Test',
                'description' => 'Evaluate how users interact with the smart lamp',
                'product_under_test' => 'Smart Lamp 3000',
                'business_case' => 'Test core usability with real users',
                'test_objectives' => 'Assess power-on, brightness, and timer functions',
                'participants' => 'Target users aged 20–40',
                'equipment' => 'Webcam + browser',
                'responsibilities' => 'Moderator + Notetaker',
                'location_dates' => 'Online / Remote — March 2025',
                'test_procedure' => 'Introduction → Tasks → Questionnaire'
            ],
            'tests' => [
                [
                    'title' => 'Lamp Control Test',
                    'description' => 'Covers core lamp interaction scenarios',
                    'layout_image' => '',
                    'task_groups' => [
                        [
                            'title' => 'Basic Controls',
                            'position' => 0,
                            'tasks' => [
                                [
                                    'task_text' => 'Turn on the lamp',
                                    'task_type' => 'text',
                                    'task_options' => '',
                                    'preset_type' => null,
                                    'script' => 'Ask the participant to power on the lamp.',
                                    'scenario' => 'You’ve just unboxed the lamp and want to use it.',
                                    'metrics' => 'Time to complete, Success',
                                    'position' => 0
                                ],
                                [
                                    'task_text' => 'Adjust brightness to 50%',
                                    'task_type' => 'radio',
                                    'task_options' => 'Success:yes;Partial:partial;Failure:no',
                                    'preset_type' => null,
                                    'script' => 'Adjust the brightness.',
                                    'scenario' => 'It’s too bright and you want softer lighting.',
                                    'metrics' => 'Accuracy, time',
                                    'position' => 1
                                ]
                            ]
                        ]
                    ],
                    'questionnaire_groups' => [
                        [
                            'title' => 'Post-Test Questions',
                            'position' => 0,
                            'questions' => [
                                [
                                    'text' => 'The lamp was easy to use.',
                                    'question_type' => 'radio',
                                    'question_options' => 'Strongly agree:5;Agree:4;Neutral:3;Disagree:2;Strongly disagree:1',
                                    'preset_type' => 'SUS',
                                    'position' => 0
                                ],
                                [
                                    'text' => 'The lamp setup was straightforward.',
                                    'question_type' => 'radio',
                                    'question_options' => 'Strongly agree:5;Agree:4;Neutral:3;Disagree:2;Strongly disagree:1',
                                    'preset_type' => 'SUS',
                                    'position' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'participants' => [
                [
                    'participant_name' => 'Alice',
                    'participant_age' => '28',
                    'participant_gender' => 'female',
                    'participant_academic_level' => 'Bachelor’s degree',
                    'custom_fields' => [
                        'device_used' => 'iPhone',
                        'prior_experience' => 'No'
                    ]
                ],
                [
                    'participant_name' => 'Bob',
                    'participant_age' => '35',
                    'participant_gender' => 'male',
                    'participant_academic_level' => 'Master’s degree',
                    'custom_fields' => [
                        'device_used' => 'Android Tablet',
                        'prior_experience' => 'Yes'
                    ]
                ]
            ],
            'custom_fields' => [
                [
                    'label' => 'Device Used',
                    'field_type' => 'text',
                    'options' => '',
                    'position' => 0
                ],
                [
                    'label' => 'Prior Experience with Smart Lamps',
                    'field_type' => 'select',
                    'options' => 'Yes;No',
                    'position' => 1
                ]
            ]
        ]
        ];

        return $templates[$name] ?? $templates['lamp'];
    }



    public function chooseTemplate()
    {
        $templates = [
        [
            'id' => 'lamp',
            'title' => '💡 Smart Lamp Usability Test',
            'description' => 'Test basic usage of a smart lamp: turning it on, changing color, scheduling.'
        ],
        [
            'id' => 'onboarding',
            'title' => '👣 App Onboarding Test',
            'description' => 'Evaluate the user onboarding experience for a new mobile app.'
        ],
        [
            'id' => 'checkout',
            'title' => '🛒 E-commerce Checkout Flow',
            'description' => 'Test usability of the checkout process on an e-commerce website.'
        ]
        ];

        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => 'Use Template', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/import/templates.php';
    }

    public function importTemplate()
    {
        $template = $_GET['template'] ?? null;

        $data = $this->loadProjectTemplate($template);
        if (!$data) {
            echo "Invalid template.";
            exit;
        }

        $project_id = $this->importFromJsonArray($data);

        header("Location: /index.php?controller=Project&action=show&id=$project_id&success=project_imported");
        exit;
    }


    private function importFromJsonArray(array $data): int
    {
        // Step 1: Insert project
        $project = $data['project'] ?? [];
        $stmt = $this->pdo->prepare(
            "
        INSERT INTO projects (
            title, description, product_under_test, business_case,
            test_objectives, participants, equipment, responsibilities,
            location_dates, test_procedure, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    "
        );
        $stmt->execute(
            [
            $project['title'] ?? 'Imported Project',
            $project['description'] ?? '',
            $project['product_under_test'] ?? '',
            $project['business_case'] ?? '',
            $project['test_objectives'] ?? '',
            $project['participants'] ?? '',
            $project['equipment'] ?? '',
            $project['responsibilities'] ?? '',
            $project['location_dates'] ?? '',
            $project['test_procedure'] ?? ''
            ]
        );
        $project_id = $this->pdo->lastInsertId();

        // Step 2a: Insert custom fields and build label → ID map
        $fieldNameToId = [];

        foreach ($data['custom_fields'] ?? [] as $field) {
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participants_custom_fields (project_id, label, field_type, options, position)
            VALUES (?, ?, ?, ?, ?)
        "
            );
            $stmt->execute(
                [
                $project_id,
                $field['label'],
                $field['field_type'],
                $field['options'] ?? '',
                $field['position'] ?? 0
                ]
            );

            $insertedId = $this->pdo->lastInsertId();
            $fieldNameToId[strtolower($field['label'])] = $insertedId;
        }

        // Step 2b: Insert tests, tasks, questions
        $firstTestId = null;

        foreach ($data['tests'] ?? [] as $test) {
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO tests (project_id, title, description, layout_image, created_at)
            VALUES (?, ?, ?, ?, NOW())
        "
            );
            $stmt->execute(
                [
                $project_id,
                $test['title'],
                $test['description'],
                $test['layout_image'] ?? null
                ]
            );
            $testId = $this->pdo->lastInsertId();
            $firstTestId = $firstTestId ?? $testId; // keep the first test ID for participant assignment

            // Task groups
            foreach ($test['task_groups'] ?? [] as $tg) {
                $stmt = $this->pdo->prepare("INSERT INTO task_groups (test_id, title, position) VALUES (?, ?, ?)");
                $stmt->execute([$testId, $tg['title'], $tg['position'] ?? 0]);
                $tgId = $this->pdo->lastInsertId();

                foreach ($tg['tasks'] ?? [] as $task) {
                    if (empty($task['task_text'])) continue; // skip invalid task
 // Convert string task to full array
 if (is_string($task)) {
    $task = [
        'task_text' => $task,
        'preset_type' => null,
        'script' => '',
        'scenario' => '',
        'metrics' => '',
        'task_type' => 'text',
        'task_options' => '',
        'position' => 0
    ];
}

                    $stmt = $this->pdo->prepare("INSERT INTO tasks (
                        task_group_id, task_text, preset_type, script, scenario, metrics, task_type, task_options, position
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                    $stmt->execute([
                        $tgId,
                        $task['task_text'] ?? '[Untitled Task]',
                        $task['preset_type'] ?? null,
                        $task['script'] ?? '',
                        $task['scenario'] ?? '',
                        $task['metrics'] ?? '',
                        $task['task_type'] ?? 'text',
                        $task['task_options'] ?? '',
                        $task['position'] ?? 0
                    ]);
                }
            }

            // Questionnaire groups
            foreach ($test['questionnaire_groups'] ?? [] as $qg) {
                $stmt = $this->pdo->prepare("INSERT INTO questionnaire_groups (test_id, title, position) VALUES (?, ?, ?)");
                $stmt->execute([$testId, $qg['title'], $qg['position'] ?? 0]);
                $qgId = $this->pdo->lastInsertId();

                foreach ($qg['questions'] ?? [] as $q) {
                    if (is_string($q)) {
                        $q = [
                            'text' => $q,
                            'question_type' => 'text',
                            'question_options' => '',
                            'position' => 0,
                            'preset_type' => null
                        ];
                    }
                
                    $stmt = $this->pdo->prepare(
                        "
                    INSERT INTO questions (
                        questionnaire_group_id, text, question_type,
                        question_options, position, preset_type
                    ) VALUES (?, ?, ?, ?, ?, ?)
                "
                    );
                    $stmt->execute(
                        [
                        $qgId,
                        $q['text'],
                        $q['question_type'],
                        $q['question_options'],
                        $q['position'] ?? 0,
                        $q['preset_type'] ?? null
                        ]
                    );
                }
            }
        }

        // Step 3: Insert participants and assign to test
        foreach ($data['participants'] ?? [] as $p) {
            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participants (
                project_id, participant_name, participant_age,
                participant_gender, participant_academic_level,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        "
            );
            $stmt->execute(
                [
                $project_id,
                $p['participant_name'],
                $p['participant_age'],
                $p['participant_gender'],
                $p['participant_academic_level']
                ]
            );
            $participantId = $this->pdo->lastInsertId();

            // Assign to test
            if (!empty($firstTestId)) {
                $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
                $stmt->execute([$participantId, $firstTestId]);
            }

            // Insert custom data values
            foreach ($p['custom_fields'] ?? [] as $fieldKey => $value) {
                $fieldId = $fieldNameToId[strtolower($fieldKey)] ?? null;

                if ($fieldId && trim($value) !== '') {
                    $stmt = $this->pdo->prepare(
                        "
                    INSERT INTO participant_custom_data (
                        participant_id, field_id, value, created_at, updated_at
                    ) VALUES (?, ?, ?, NOW(), NOW())
                "
                    );
                    $stmt->execute([$participantId, $fieldId, $value]);
                }
            }
        }

        return $project_id;
    }


    public function processFile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['import_file']['tmp_name'])) {
            echo "No file uploaded.";
            exit;
        }

        $json = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($json, true);

        if (!$data || !is_array($data)) {
            echo "Invalid or corrupt file.";
            exit;
        }

        $project_id = $this->importFromJsonArray($data);

        header("Location: /index.php?controller=Project&action=show&id=$project_id&success=project_imported");
        exit;
    }

    public function importParticipantsForm()
    {
        $project_id = $_GET['project_id'] ?? null;
    
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$project) {
            echo "Project not found.";
            exit;
        }
    
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
            ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project['id'], 'active' => false],
            ['label' => 'Import Participants (CSV)', 'url' => '', 'active' => true],
        ];
    
        include __DIR__ . '/../views/import/import_participants.php';
    }

    public function importParticipantsCSV()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            echo "No CSV file uploaded.";
            exit;
        }

        $project_id = $_POST['project_id'] ?? null;
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $headers = fgetcsv($file); // First line as header

        $expectedHeaders = ['participant_name', 'participant_age', 'participant_gender', 'participant_academic_level', 'test_ids'];

        if (array_map('strtolower', $headers) !== $expectedHeaders) {
            echo "Invalid CSV headers. Expected: " . implode(", ", $expectedHeaders);
            exit;
        }

        while ($row = fgetcsv($file)) {
            list($name, $age, $gender, $level, $testIdsCSV) = $row;

            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participants (
                project_id, participant_name, participant_age,
                participant_gender, participant_academic_level, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        "
            );
            $stmt->execute([$project_id, $name, $age, $gender, $level]);

            $participantId = $this->pdo->lastInsertId();

            // Assign tests
            if (!empty($testIdsCSV)) {
                $testIds = array_map('trim', explode(',', $testIdsCSV));
                $stmt = $this->pdo->prepare("INSERT INTO participant_test (participant_id, test_id) VALUES (?, ?)");
                foreach ($testIds as $testId) {
                    if (is_numeric($testId)) {
                        $stmt->execute([$participantId, $testId]);
                    }
                }
            }
        }

        fclose($file);

        header("Location: /index.php?controller=Project&action=show&id=$project_id&success=participants_imported#participant-list");
        exit;
    }

    public function downloadSampleParticipantsCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sample_participants.csv"');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, ['participant_name', 'participant_age', 'participant_gender', 'participant_academic_level', 'test_ids']);

        // Example row
        fputcsv($output, ['participant_name', 'participant_age', 'participant_gender', 'participant_academic_level', 'test_ids']);
        fputcsv($output, ['Alice', '22', 'female', 'Bachelors degree', '1']);
        fputcsv($output, ['Bob', '28', 'male', 'Masters degree', '1,2']);

        fclose($output);
        exit;
    }

    public function importTasksForm()
    {
        $project_id = $_GET['project_id'] ?? null;

        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        $stmt = $this->pdo->prepare(
            "
        SELECT p.title AS project_title
        FROM projects p
        WHERE p.id = ?
    "
        );
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            echo "Project not found.";
            exit;
        }

        $stmt = $this->pdo->prepare(
            "
        SELECT tg.id, tg.title, t.title AS test_title
        FROM task_groups tg
        JOIN tests t ON t.id = tg.test_id
        WHERE t.project_id = ?
        ORDER BY t.title, tg.title
    "
        );
        $stmt->execute([$project_id]);
        $taskGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => $project['title'], 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
        ['label' => 'Import Tasks (CSV)', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/import/import_tasks.php';
    }

    public function importTasksCSV()
    {

        $project_id = $_POST['project_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            echo "No CSV file uploaded.";
            exit;
        }

        $taskGroupId = $_POST['task_group_id'] ?? null;
        if (!$taskGroupId) {
            echo "Missing task group ID.";
            exit;
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $headers = fgetcsv($file);

        $expectedHeaders = ['task_text', 'task_type', 'task_options', 'preset_type', 'script', 'scenario', 'metrics', 'position'];

        if (array_map('strtolower', $headers) !== $expectedHeaders) {
            echo "Invalid CSV headers. Expected: " . implode(", ", $expectedHeaders);
            exit;
        }

        while ($row = fgetcsv($file)) {
            list($text, $type, $options, $preset, $script, $scenario, $metrics, $position) = $row;

            $stmt = $this->pdo->prepare(
                "
            INSERT INTO tasks (
                task_group_id, task_text, task_type, task_options, preset_type, script, scenario, metrics, position
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
            );
            $stmt->execute(
                [
                $taskGroupId,
                $text,
                $type,
                $options,
                $preset,
                $script,
                $scenario,
                $metrics,
                $position
                ]
            );
        }

        fclose($file);

        // Redirect to test
        $stmt = $this->pdo->prepare("SELECT t.test_id FROM task_groups t WHERE id = ?");
        $stmt->execute([$taskGroupId]);
        $testId = $stmt->fetchColumn();

        header("Location: /index.php?controller=Project&action=show&id=$project_id&success=tasks_imported#tests-list");
        exit;
    }

    public function downloadSampleTasksCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sample_tasks.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['task_text', 'task_type', 'task_options', 'preset_type', 'script', 'scenario', 'metrics', 'position']);
        fputcsv($output, ['Plug in the lamp', 'text', '', '', 'Please plug it in.', 'You are unpacking it for the first time.', 'Time to complete', '0']);

        fclose($output);
        exit;
    }

    public function importQuestionsForm()
    {

        $project_id = $_POST['project_id'] ?? null;

        $stmt = $this->pdo->query(
            "
        SELECT qg.id, qg.title, t.title AS test_title
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        ORDER BY t.title, qg.title
    "
        );
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/import/import_questions.php';
    }

    public function importQuestionsCSV()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            echo "No CSV file uploaded.";
            exit;
        }

        $groupId = $_POST['questionnaire_group_id'] ?? null;
        if (!$groupId) {
            echo "Missing questionnaire group ID.";
            exit;
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $headers = fgetcsv($file);

        $expectedHeaders = ['text', 'question_type', 'question_options', 'preset_type', 'position'];

        if (array_map('strtolower', $headers) !== $expectedHeaders) {
            echo "Invalid CSV headers. Expected: " . implode(", ", $expectedHeaders);
            exit;
        }

        while ($row = fgetcsv($file)) {
            list($text, $type, $options, $preset, $position) = $row;

            $stmt = $this->pdo->prepare(
                "
            INSERT INTO questions (
                questionnaire_group_id, text, question_type, question_options, preset_type, position
            ) VALUES (?, ?, ?, ?, ?, ?)
        "
            );
            $stmt->execute(
                [
                $groupId,
                $text,
                $type,
                $options,
                $preset,
                $position
                ]
            );
        }

        fclose($file);

        // Redirect to test
        $stmt = $this->pdo->prepare("SELECT t.test_id FROM questionnaire_groups t WHERE id = ?");
        $stmt->execute([$groupId]);
        $testId = $stmt->fetchColumn();

        header("Location: /index.php?controller=Test&action=show&id=" . $testId);
        exit;
    }


    public function downloadSampleQuestionsCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sample_questions.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['text', 'question_type', 'question_options', 'preset_type', 'position']);
        fputcsv($output, ['How satisfied are you?', 'radio', 'Very:Satisfied;Neutral;Unsatisfied', '', '0']);

        fclose($output);
        exit;
    }

    public function importCustomFieldsForm()
    {
        $stmt = $this->pdo->query("SELECT id, title FROM projects ORDER BY created_at DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/import/import_custom_fields.php';
    }

    public function importCustomFieldsCSV()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['csv_file']['tmp_name'])) {
            echo "No CSV file uploaded.";
            exit;
        }

        $project_id = $_POST['project_id'] ?? null;
        if (!$project_id) {
            echo "Missing project ID.";
            exit;
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $headers = fgetcsv($file);

        $expectedHeaders = ['label', 'field_type', 'options', 'position'];

        if (array_map('strtolower', $headers) !== $expectedHeaders) {
            echo "Invalid CSV headers. Expected: " . implode(", ", $expectedHeaders);
            exit;
        }

        while ($row = fgetcsv($file)) {
            list($label, $fieldType, $options, $position) = $row;

            $stmt = $this->pdo->prepare(
                "
            INSERT INTO participants_custom_fields (
                project_id, label, field_type, options, position
            ) VALUES (?, ?, ?, ?, ?)
        "
            );
            $stmt->execute([$project_id, $label, $fieldType, $options, $position]);
        }

        fclose($file);

        header("Location: /index.php?controller=Project&action=show&id=$project_id&success=custom_fields_imported");
        exit;
    }

    public function downloadSampleCustomFieldsCSV()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sample_custom_fields.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['label', 'field_type', 'options', 'position']);
        fputcsv($output, ['Hand size', 'number', '', '0']);
        fputcsv($output, ['Preferred platform', 'select', 'Windows;macOS;Linux', '1']);

        fclose($output);
        exit;
    }

    public function uploadJSONForm()
    {
        $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => 'Import Project (JSON)', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/import/upload_json.php';
    }

    public function aiForm()
{
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=index', 'active' => false],
        ['label' => 'AI Generator', 'url' => '', 'active' => true],
    ];

    include __DIR__ . '/../views/import/ai_form.php';
}

public function getSetting($key)
{
    $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn();
}


public function generateFromPrompt()
{
    $prompt = trim($_POST['prompt'] ?? '');
    if (!$prompt) {
        echo "⚠️ Prompt missing.";
        exit;
    }

    $apiKey = getOpenAIKey($this->pdo);
    if (!$apiKey) {
        echo "❌ OpenAI API key not configured.";
        exit;
    }

    $systemMessage = "You are an expert usability test designer. Your task is to generate a complete JSON project structure including all metadata fields, multiple task groups and questionnaires. Follow the structure carefully.";

    $fullPrompt = <<<EOT
Generate a JSON usability testing project for this scenario:

"$prompt"

Your output **must** use this format:

{
  "project": {
    "title": "...",
    "description": "...",
    "product_under_test": "...",
    "business_case": "...",
    "test_objectives": "...",
    "participants": "...",
    "equipment": "...",
    "responsibilities": "...",
    "location_dates": "...",
    "test_procedure": "..."
  },
  "tests": [
    {
      "title": "...",
      "description": "...",
      "layout_image": "",
      "task_groups": [
        {
          "title": "...",
          "position": 0,
          "tasks": [
            {
              "task_text": "...",
              "preset_type": null,
              "script": "...",
              "scenario": "...",
              "metrics": "...",
              "task_type": "text",
              "task_options": "",
              "position": 0
            }
          ]
        }
      ],
      "questionnaire_groups": [
        {
          "title": "...",
          "position": 0,
          "questions": [
            {
              "text": "...",
              "question_type": "radio",
              "question_options": "Very easy:5;Easy:4;Neutral:3;Hard:2;Very hard:1",
              "position": 0,
              "preset_type": null
            }
          ]
        }
      ]
    }
  ],
  "participants": []
}

Additional requirements:
- Each test should contain at least **1 task group** with **5 tasks**.
- Each test should contain at least **1 questionnaire group** with **5 questions**.
- Tasks and questions must be fully filled out (no placeholders).
- Respond with valid **JSON only**, no explanations or comments.

EOT;

    $response = callOpenAI($apiKey, $systemMessage, $fullPrompt);

    $jsonData = json_decode($response, true);
    if (!$jsonData || !is_array($jsonData)) {
        echo "<pre>⚠️ Invalid AI response:\n" . htmlspecialchars($response) . "</pre>";
        exit;
    }

    // Optional: save last AI response for debugging
    file_put_contents(__DIR__ . '/../logs/ai_latest.json', json_encode($jsonData, JSON_PRETTY_PRINT));

    $projectId = $this->importFromJsonArray($jsonData);
    header("Location: /index.php?controller=Project&action=show&id=$projectId");
    exit;
}

}
