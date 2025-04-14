<?php
// Configuration variables
require_once '../config/db.php';

$messages = [];
$adminCreated = false;

try {
    $pdo = new PDO("mysql:host=$host", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $messages[] = "✅ Database <strong>$dbname</strong> created or already exists.";

    $pdo->exec("USE `$dbname`");

    // Table definitions
    $tables = [

        // === TABLE: projects ===
        "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            product_under_test TEXT NOT NULL,
            business_case TEXT NOT NULL,
            test_objectives TEXT NOT NULL,
            participants TEXT NOT NULL,
            equipment TEXT NOT NULL,
            responsibilities TEXT NOT NULL,
            location_dates TEXT NOT NULL,
            test_procedure TEXT NOT NULL
        ) ENGINE=InnoDB;" => "projects",

        // === TABLE: tests ===
        "CREATE TABLE IF NOT EXISTS tests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            layout_image VARCHAR(255),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "tests",

        // === TABLE: moderators ===
        "CREATE TABLE IF NOT EXISTS moderators (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login DATETIME DEFAULT NULL,
            last_login_ip VARCHAR(45) DEFAULT NULL,
            last_login_user_agent VARCHAR(255) DEFAULT NULL,
            last_login_location VARCHAR(255) DEFAULT NULL,
            fullname VARCHAR(255) DEFAULT NULL,
            company VARCHAR(255) DEFAULT NULL,
            password_hash VARCHAR(255) NOT NULL,
            reset_token VARCHAR(255)  DEFAULT NULL,
            is_superadmin BOOLEAN NOT NULL DEFAULT 0,
            is_admin BOOLEAN NOT NULL DEFAULT 0
        ) ENGINE=InnoDB;" => "moderators",
        
        // === TABLE: project_user ===
        "CREATE TABLE IF NOT EXISTS project_user (
            project_id INT NOT NULL,
            moderator_id INT NOT NULL,
            PRIMARY KEY (project_id, moderator_id),
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "project_user",

        // === TABLE: moderator_test ===
        "CREATE TABLE IF NOT EXISTS moderator_test (
            moderator_id INT NOT NULL,
            test_id INT NOT NULL,
            PRIMARY KEY (moderator_id, test_id),
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "moderator_test",

        // === TABLE: task_groups ===
        "CREATE TABLE IF NOT EXISTS task_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            position INT DEFAULT 0,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "task_groups",

        // === TABLE: tasks ===
        "CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_group_id INT NOT NULL,
            task_text TEXT NOT NULL,
            preset_type VARCHAR(50) DEFAULT NULL,
            script TEXT,
            scenario TEXT,
            metrics TEXT,
            task_type ENUM('text', 'radio', 'checkbox', 'dropdown') DEFAULT 'text',
            task_options TEXT,
            position INT DEFAULT 0,
            FOREIGN KEY (task_group_id) REFERENCES task_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "tasks",

        // === TABLE: questionnaire_groups ===
        "CREATE TABLE IF NOT EXISTS questionnaire_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            position INT DEFAULT 0,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "questionnaire_groups",

        // === TABLE: questions ===
        "CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            questionnaire_group_id INT NOT NULL,
            text TEXT NOT NULL,
            question_type ENUM('text', 'radio', 'checkbox', 'dropdown') DEFAULT 'text',
            question_options TEXT,
            position INT DEFAULT 0,
            preset_type VARCHAR(50) DEFAULT NULL,
            FOREIGN KEY (questionnaire_group_id) REFERENCES questionnaire_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "questions",

        // === TABLE: evaluations ===
        "CREATE TABLE IF NOT EXISTS evaluations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            timestamp DATETIME NOT NULL,
            moderator_observations TEXT,
            participant_name VARCHAR(255),
            participant_age VARCHAR(20) DEFAULT NULL,
            participant_gender VARCHAR(255),
            participant_academic_level VARCHAR(100) DEFAULT NULL,
            did_tasks BOOLEAN DEFAULT NULL,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "evaluations",

        // === TABLE: responses ===
        "CREATE TABLE IF NOT EXISTS responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            time_spent INT NOT NULL,
            evaluation_errors TEXT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "responses",

        // === TABLE: tests_custom_fields ===
        "CREATE TABLE IF NOT EXISTS test_custom_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            label VARCHAR(100) NOT NULL,
            field_type ENUM('text', 'number', 'select') DEFAULT 'text',
            options TEXT,
            position INT DEFAULT 0,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "test_custom_fields",

        // === TABLE: evaluation_custom_data ===
        "CREATE TABLE IF NOT EXISTS evaluation_custom_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            field_id INT NOT NULL,
            value TEXT NOT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
            FOREIGN KEY (field_id) REFERENCES test_custom_fields(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "evaluation_custom_data",

        // === TABLE: Participant table ===
        "CREATE TABLE IF NOT EXISTS participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            test_id INT DEFAULT NULL,
            participant_name VARCHAR(255),
            participant_age VARCHAR(20),
            participant_gender VARCHAR(255),
            participant_academic_level VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE SET NULL
        ) ENGINE=InnoDB;" => "participants",

        // === TABLE: test_sessions ===
        "CREATE TABLE IF NOT EXISTS test_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            participant_id INT NOT NULL,
            session_start DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            session_end DATETIME DEFAULT NULL,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "test_sessions",

    ];

    foreach ($tables as $sql => $name) {
        $pdo->exec($sql);
        $messages[] = "Table '<strong>$name</strong>' created or already exists.";
    }


    // Admin user

    $stmt = $pdo->prepare("SELECT id FROM moderators WHERE username = ?");
    $stmt->execute(['testgod']);

    if ($stmt->rowCount() === 0) {
        $passwordHash = password_hash('testgod', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO moderators (fullname, company, email, username, password_hash, is_admin, is_superadmin) VALUES (?, ?, ?,?,?,?,?)");
        $stmt->execute(['Test God', 'TestFlowUX','testgod@testflow.desing','testgod', $passwordHash, 1, 1]);
        $adminCreated = true;
    }

} catch (PDOException $e) {
    echo "<pre style='color:red'>Error: " . $e->getMessage() . "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TestFlow Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5">🛠️ TestFlow Setup</h1>
        <p class="lead text-muted">Database initialized and tables verified successfully.</p>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">

            <div class="alert alert-info">
                ✅ Database <strong><?php echo htmlspecialchars($dbname); ?></strong> is ready.
            </div>

            <ul class="list-group mb-4 shadow-sm">
                <?php foreach ($messages as $msg): ?>
                    <li class="list-group-item"><?php echo $msg; ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if ($adminCreated): ?>
                <div class="alert alert-success">👤 Admin user <strong>testgod</strong> created successfully.</div>
            <?php else: ?>
                <div class="alert alert-secondary">👤 Admin user <strong>testgod</strong> already exists.</div>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a href="../index.php" class="btn btn-primary btn-lg">
                    🚀 Go to Admin Dashboard
                </a>
            </div>

        </div>
    </div>
</div>
</body>
</html>
