<?php
// Configuration variables
require_once '../config/db.php';

try {
    // Connect to MySQL/MariaDB server (without specifying a database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created successfully or already exists.<br>";

    // Connect to the newly created database
    $pdo->exec("USE `$dbname`");

    // Create 'tests' table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT
        ) ENGINE=InnoDB;
    ");
    echo "Table 'tests' created successfully or already exists.<br>";

    // Create 'moderators' table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS moderators (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;
    ");
    echo "Table 'moderators' created successfully or already exists.<br>";

    // Create pivot table for many-to-many: moderator_test
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS moderator_test (
            moderator_id INT NOT NULL,
            test_id INT NOT NULL,
            PRIMARY KEY (moderator_id, test_id),
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'moderator_test' created successfully or already exists.<br>";

    // Create 'questions' table if it doesn't exist (minimal columns)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            text VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;
    ");
    echo "Table 'questions' created successfully or already exists.<br>";

    // Ensure 'test_id' column exists in 'questions'
    $columnCheck = $pdo->query("SHOW COLUMNS FROM questions LIKE 'test_id'");
    if ($columnCheck->rowCount() === 0) {
        // If 'test_id' column is missing, add it + foreign key
        $pdo->exec("
            ALTER TABLE questions
            ADD COLUMN test_id INT NOT NULL DEFAULT 1,
            ADD CONSTRAINT fk_questions_test
                FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ");
        echo "Added 'test_id' column to 'questions' and linked it to 'tests'.<br>";
    }

    // Create 'evaluations' table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS evaluations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id INT NOT NULL,
            timestamp DATETIME NOT NULL,
            observation TEXT,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'evaluations' created successfully or already exists.<br>";

    // Create 'responses' table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS responses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            question VARCHAR(255) NOT NULL,
            answer TEXT NOT NULL,
            time_spent INT NOT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "Table 'responses' created successfully or already exists.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
