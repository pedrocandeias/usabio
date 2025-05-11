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
            is_admin BOOLEAN NOT NULL DEFAULT 0,
            user_type VARCHAR(20) DEFAULT 'none',
            is_confirmed TINYINT(1) DEFAULT 0,
            confirmation_token VARCHAR(64) DEFAULT NULL
        ) ENGINE=InnoDB;" => "moderators",
        
   // === TABLE: settings ===
"CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;" => "settings",

// === INSERT default settings ===
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'openai_api_key', '') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'openai_api_key') LIMIT 1;" => "openai_api_key settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'platform_base_url', 'https://usabio.ddev.site') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'platform_base_url') LIMIT 1;" => "platform_base_url settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'max_projects_per_user', '3') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'max_projects_per_user') LIMIT 1;" => "max_projects_per_user settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'max_projects_per_normal_user', '1') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'max_projects_per_normal_user') LIMIT 1;" => "max_projects_per_normal_user settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'max_projects_per_premium_user', '3') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'max_projects_per_premium_user') LIMIT 1;" => "max_projects_per_premium_user settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'max_projects_per_superpremium_user', '9') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'max_projects_per_superpremium_user') LIMIT 1;" => "max_projects_per_superpremium_user settings",

"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'enable_ai_features', '1') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'enable_ai_features') LIMIT 1;" => "enable_ai_features settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'enable_user_registration', '1') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'enable_user_registration') LIMIT 1;" => "enable_user_registration settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'enable_login', '1') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'enable_login') LIMIT 1;" => "enable_login settings",

"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'default_language', 'en') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'default_language') LIMIT 1;" => "default_language settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'ui_theme', '') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'ui_theme') LIMIT 1;" => "ui_theme settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'feature_flags_json', '') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'feature_flags_json') LIMIT 1;" => "feature_flags_json settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'allow_public_registration', '1') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'allow_public_registration') LIMIT 1;" => "allow_public_registration settings",

"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'mailserver_host', 'mail.testflow.design') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'mailserver_host') LIMIT 1;" => "mailserver_host settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'mailserver_port', '465') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'mailserver_port') LIMIT 1;" => "mailserver_port settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'mailserver_username', 'noreply@testflow.design') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'mailserver_username') LIMIT 1;" => "mailserver_username settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'mailserver_password', '') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'mailserver_password') LIMIT 1;" => "mailserver_password settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'mailserver_encryption', 'ssl') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'mailserver_encryption') LIMIT 1;" => "mailserver_encryption settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'noreplymail', 'noreply@testflow.design') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'noreplymail') LIMIT 1;" => "settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'platform_name', 'TestFlow') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'platform_name') LIMIT 1;" => "settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'support_email', 'sayhi@testflow.design') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'support_email') LIMIT 1;" => "settings",
"INSERT INTO settings (setting_key, setting_value)
SELECT * FROM (SELECT 'test_email', 'sayhi@testflow.design') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE setting_key = 'test_email') LIMIT 1;" => "settings",

        // === TABLE: projects ===
      "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        owner_id INT NOT NULL,
        status ENUM('complete', 'inprogress') DEFAULT 'inprogress',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        product_under_test TEXT NOT NULL,
        business_case TEXT NOT NULL,
        test_objectives TEXT NOT NULL,
        participants TEXT NOT NULL,
        equipment TEXT NOT NULL,
        responsibilities TEXT NOT NULL,
        location_dates TEXT NOT NULL,
        test_procedure TEXT NOT NULL,
        project_image VARCHAR(255),
        CONSTRAINT fk_projects_owner FOREIGN KEY (owner_id) REFERENCES moderators(id)
            ON DELETE CASCADE
            ON UPDATE RESTRICT
    ) ENGINE=InnoDB;" => "projects",


        // === TABLE: tests ===
        "CREATE TABLE IF NOT EXISTS tests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            layout_image VARCHAR(255),
            status VARCHAR(50) DEFAULT 'draft',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "tests",

        // === TABLE: project_user ===
        "CREATE TABLE IF NOT EXISTS project_user (
            project_id INT NOT NULL,
            moderator_id INT NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            PRIMARY KEY (project_id, moderator_id),
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "project_user",

       
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
            did_questionnaire BOOLEAN DEFAULT NULL,
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
            type ENUM('task', 'questionnaire') NOT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "responses",

        // === TABLE: tests_custom_fields ===
        "CREATE TABLE IF NOT EXISTS participants_custom_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            label VARCHAR(100) NOT NULL,
            field_type ENUM('text', 'number', 'select') DEFAULT 'text',
            options TEXT,
            position INT DEFAULT 0,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "participants_custom_fields",

        // === TABLE: evaluation_custom_data ===
        "CREATE TABLE IF NOT EXISTS evaluation_custom_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            field_id INT NOT NULL,
            value TEXT NOT NULL,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
            FOREIGN KEY (field_id) REFERENCES participants_custom_fields(id) ON DELETE CASCADE
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

        // === TABLE: participants_test ===
        "CREATE TABLE IF NOT EXISTS participant_test (
            participant_id INT NOT NULL,
            test_id INT NOT NULL,
            PRIMARY KEY (participant_id, test_id),
            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
            FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "participant_test",

        // === TABLE: participant_custom_data ===
        "CREATE TABLE IF NOT EXISTS participant_custom_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            participant_id INT NOT NULL,
            field_id INT NOT NULL,
            value TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
            FOREIGN KEY (field_id) REFERENCES participants_custom_fields(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;" => "participant_custom_data",

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

     

    // === TABLE: Project Invites ===
    "CREATE TABLE IF NOT EXISTS project_invites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        moderator_id INT NOT NULL,
        status ENUM('pending','accepted','declined') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY ux_prj_mod (project_id, moderator_id),
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (moderator_id) REFERENCES moderators(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;"    => "project_invites",    
   

    // === TABLE: pending_invite_emails ===
    "CREATE TABLE IF NOT EXISTS pending_invite_emails (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        status ENUM('sent','registered') DEFAULT 'sent',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;" => "pending_invite_emails",

    // === TABLE: email_templates ===
    "CREATE TABLE IF NOT EXISTS email_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        template_key VARCHAR(100) NOT NULL UNIQUE,
        subject TEXT NOT NULL,
        body TEXT NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;" => "email_templates",
// === INSERT default email templates ===
"INSERT INTO email_templates (template_key, subject, body)
SELECT * FROM (SELECT 'test_email', 'Test Email from {{platform_name}}', '<p>Hello,</p><p>This is a test email confirming your mailserver settings are working.</p><p>If you received this, everything is set up correctly 🎉</p>') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM email_templates WHERE template_key = 'test_email') LIMIT 1;" => "email_templates",

"INSERT INTO email_templates (template_key, subject, body)
SELECT * FROM (SELECT 'registration_confirmation', 'Welcome to {{platform_name}}', '<p>Hello {{fullname}},</p><p>Thank you for registering on <strong>{{platform_name}}</strong>.</p><p>You can now access your dashboard and start participating in projects.</p>') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM email_templates WHERE template_key = 'registration_confirmation') LIMIT 1;" => "email_templates",

"INSERT INTO email_templates (template_key, subject, body)
SELECT * FROM (SELECT 'invite_accepted_notification', 'Moderator {{fullname}} accepted your project invite', '<p>Hello,</p><p><strong>{{fullname}}</strong> has accepted the invitation to join your project: <strong>{{project_title}}</strong>.</p><p>You can now assign tasks and start testing.</p>') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM email_templates WHERE template_key = 'invite_accepted_notification') LIMIT 1;" => "email_templates",

"INSERT INTO email_templates (template_key, subject, body)
SELECT * FROM (
  SELECT
    'invite_email',
    'You''ve been invited to join a project on {{platform_name}}',
    '<p>Hello,</p>
     <p>You''ve been invited to participate as a moderator in a project on <strong>{{platform_name}}</strong>.</p>
     <p>Click the button below to register and accept the invitation:</p>
     <p><a href=\"{{register_url}}\" class=\"btn btn-primary\">Join Us</a></p>
     <p>If you already have an account, you can ignore this message.</p>'
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM email_templates WHERE template_key = 'invite_email'
) LIMIT 1;" => "email_templates",

    ];
    // Create tables if they don't exist


    foreach ($tables as $sql => $name) {
        $pdo->exec($sql);
        $messages[] = "Table '<strong>$name</strong>' created or already exists.";
    }


    // Admin user

    $stmt = $pdo->prepare("SELECT id FROM moderators WHERE username = ?");
    $stmt->execute(['testgod']);

    if ($stmt->rowCount() === 0) {
        $passwordHash = password_hash('testgod', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO moderators (fullname, company, email, username, password_hash, is_admin, is_superadmin, is_confirmed) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute(['Test God', 'TestFlowUX','testgod@testflow.desing','testgod', $passwordHash, 1, 1, 1]);
        $adminCreated = true;
    }

} catch (PDOException $e) {
    echo "<pre style='color:red'>Error: " . $e->getMessage() . "</pre>";
    exit;
}
// Write .env file if it doesn't exist
$envPath = dirname(__DIR__) . '/.env';
if (!file_exists($envPath)) {
    $secretKey = bin2hex(random_bytes(32)); // Generate a 64-character random key
    $defaultEnv = <<<ENV
SUPER_SECRET_KEY=$secretKey

ENV;

    file_put_contents($envPath, $defaultEnv);
    $messages[] = "📄 <strong>.env</strong> file created successfully in the project root.";
} else {
    $messages[] = "📄 <strong>.env</strong> file already exists. Skipped creation.";
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
