<?php
// Configuration variables
require_once '../config/db.php';

try {
    $pdo = new PDO("mysql:host=$host", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created or already exists.<br>";

    $pdo->exec("USE `$dbname`");

    // === TABLE: projects ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_under_test TEXT NOT NULL,
            business_case TEXT NOT NULL,
            test_objectives TEXT NOT NULL,
            participants TEXT NOT NULL,
            equipment TEXT NOT NULL,
            responsibilities TEXT NOT NULL,
            location_dates TEXT NOT NULL,
            test_procedure TEXT NOT NULL
        ) ENGINE=InnoDB;
    ");
    echo "Table 'projects' created or already exists.<br>";

    // === TABLE: tests ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            layout_image VARCHAR(255),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'tests' created or already exists.<br>";

    // === TABLE: moderators ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS moderators (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            is_admin BOOLEAN NOT NULL DEFAULT 0
        ) ENGINE=InnoDB;
    ");
    echo "Table 'moderators' created or already exists.<br>";

    // === TABLE: moderator_test ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS moderator_test (
            moderator_id INT NOT NULL,
            test_id INT NOT NULL,
            PRIMARY KEY (moderator_id, test_id),
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'moderator_test' created or already exists.<br>";

    // === TABLE: task_groups ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS task_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            position INT DEFAULT 0,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'task_groups' created or already exists.<br>";

    // === TABLE: tasks ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_group_id INT NOT NULL,
            task_text TEXT NOT NULL,
            script TEXT,
            scenario TEXT,
            metrics TEXT,
            task_type ENUM('text', 'radio', 'checkbox', 'dropdown') DEFAULT 'text',
            task_options TEXT,
            position INT DEFAULT 0,
            FOREIGN KEY (task_group_id) REFERENCES task_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'tasks' created or already exists.<br>";

    // === TABLE: questionnaire_groups ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questionnaire_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            position INT DEFAULT 0,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'questionnaire_groups' created or already exists.<br>";

    // === TABLE: questions ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            questionnaire_group_id INT NOT NULL,
            text TEXT NOT NULL,
            question_type ENUM('text', 'radio', 'checkbox', 'dropdown') DEFAULT 'text',
            question_options TEXT,
            position INT DEFAULT 0,
            FOREIGN KEY (questionnaire_group_id) REFERENCES questionnaire_groups(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'questions' created or already exists.<br>";

    // === TABLE: evaluations ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS evaluations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            timestamp DATETIME NOT NULL,
            moderator_observations TEXT,
            participant_name VARCHAR(255),
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'evaluations' created or already exists.<br>";

    // === TABLE: responses ===
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            time_spent INT NOT NULL,
            evaluation_errors TEXT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'responses' created or already exists.<br>";

    // === Admin user 'testgod' ===
    $stmt = $pdo->prepare("SELECT id FROM moderators WHERE username = ?");
    $stmt->execute(['testgod']);

    if ($stmt->rowCount() === 0) {
        $passwordHash = password_hash('testgod', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO moderators (username, password_hash, is_admin) VALUES (?, ?, ?)");
        $stmt->execute(['testgod', $passwordHash, 1]);
        echo "Admin user 'testgod' created successfully.<br>";
    } else {
        echo "Admin user 'testgod' already exists.<br>";
    }

    echo "<br>All tables verified or updated successfully!<br><br>";
    echo "<a href='../admin/index.php'>Go to Admin Dashboard</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
