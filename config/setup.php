<?php
// Configuration variables
require_once '../config/db.php';

try {
    $pdo = new PDO("mysql:host=$host", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created or already exists.<br>";

    $pdo->exec("USE `$dbname`");

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS tests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;"
    );
    echo "Table 'tests' created or already exists.<br>";

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS moderators (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        is_admin BOOLEAN NOT NULL DEFAULT 0
    ) ENGINE=InnoDB;"
    );
    echo "Table 'moderators' created or already exists.<br>";

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS moderator_test (
        moderator_id INT NOT NULL,
        test_id INT NOT NULL,
        PRIMARY KEY (moderator_id, test_id),
        FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE,
        FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;"
    );
    echo "Table 'moderator_test' created or already exists.<br>";

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB;"
    );
    echo "Table 'questions' created or already exists.<br>";

    $colCheckQuestionsTestId = $pdo->query("SHOW COLUMNS FROM questions LIKE 'test_id'");
    if ($colCheckQuestionsTestId->rowCount() === 0) {
        $pdo->exec(
            "ALTER TABLE questions
            ADD COLUMN test_id INT NOT NULL DEFAULT 1,
            ADD CONSTRAINT fk_questions_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE"
        );
        echo "Added 'test_id' column + foreign key to 'questions'.<br>";
    }

    $colCheckQType = $pdo->query("SHOW COLUMNS FROM questions LIKE 'question_type'");
    if ($colCheckQType->rowCount() === 0) {
        $pdo->exec("ALTER TABLE questions ADD COLUMN question_type ENUM('task','question') NOT NULL DEFAULT 'question'");
        echo "Added 'question_type' column to 'questions'.<br>";
    }
    // Add 'script' columns to 'questions' table if they don't exist
    $colCheckScript = $pdo->query("SHOW COLUMNS FROM questions LIKE 'script'");
    if ($colCheckScript->rowCount() === 0) {
        $pdo->exec("ALTER TABLE questions ADD COLUMN script TEXT NULL");
        echo "Added 'script' column to 'questions'.<br>";
    }

    $colCheckScenario= $pdo->query("SHOW COLUMNS FROM questions LIKE 'scenario'");
    if ($colCheckScenario->rowCount() === 0) {
        $pdo->exec("ALTER TABLE questions ADD COLUMN scenario TEXT NULL");
        echo "Added 'scenario' column to 'questions'.<br>";
    }

    $colCheckPosition= $pdo->query("SHOW COLUMNS FROM questions LIKE 'position'");
    if ($colCheckPosition->rowCount() === 0) {
        $pdo->exec("ALTER TABLE questions ADD COLUMN position INT NOT NULL DEFAULT 0");
        echo "Added 'position' column to 'questions'.<br>";
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS evaluations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_id INT NOT NULL,
        timestamp DATETIME NOT NULL,
        moderator_observations TEXT,
        participant_name VARCHAR(255),
        FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;"
    );
    echo "Table 'evaluations' created or updated.<br>";

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        evaluation_id INT NOT NULL,
        question VARCHAR(255) NOT NULL,
        answer TEXT NOT NULL,
        time_spent INT NOT NULL,
        FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;"
    );
    echo "Table 'responses' created or already exists.<br>";

    // Insert default admin user 'testgod' if not exists
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
