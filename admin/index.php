<?php
session_start();

// --- Simple Admin Auth (still basic)
$admin_password = "admin123"; // Change this!
if (isset($_POST['login']) && $_POST['password'] === $admin_password) {
    $_SESSION['logged_in'] = true;
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['logged_in'])) {
    echo <<<HTML
    <form method="post" style="max-width: 300px; margin: 50px auto; font-family: sans-serif;">
        <h3>Admin Login</h3>
        <input type="password" name="password" class="form-control mb-2" placeholder="Password">
        <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
HTML;
    exit;
}

// --- DB Setup
require_once '../config/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = null;

    // ========== CREATE NEW TEST ==========
    if (isset($_POST['new_test']) && !empty($_POST['test_title'])) {
        $stmt = $pdo->prepare("INSERT INTO tests (title, description) VALUES (?, ?)");
        $stmt->execute([$_POST['test_title'], $_POST['test_description'] ?? null]);
        $message = "New test created!";
    }

    // ========== ADD NEW QUESTION ==========
    if (isset($_POST['new_question'], $_POST['test_id']) && !empty($_POST['new_question'])) {
        $stmt = $pdo->prepare("INSERT INTO questions (test_id, text) VALUES (?, ?)");
        $stmt->execute([$_POST['test_id'], $_POST['new_question']]);
        $message = "Question added!";
    }

    // ========== DELETE OR EDIT QUESTION ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['question_id'])) {
        $question_id = (int) $_POST['question_id'];

        // --- Delete question
        if ($_POST['action'] === 'delete_question') {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? LIMIT 1");
            $stmt->execute([$question_id]);
            $message = "Question #{$question_id} deleted!";
        }

        // --- Edit question
        elseif ($_POST['action'] === 'edit_question' && !empty($_POST['updated_text'])) {
            $updated_text = trim($_POST['updated_text']);
            $stmt = $pdo->prepare("UPDATE questions SET text = ? WHERE id = ?");
            $stmt->execute([$updated_text, $question_id]);
            $message = "Question #{$question_id} updated!";
        }
    }

    // ========== FETCH TESTS ==========
    $stmt = $pdo->query("SELECT * FROM tests ORDER BY id DESC");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ========== LOAD QUESTIONS FOR SELECTED TEST ==========
    $selected_test_id = $_POST['test_id'] ?? $_GET['test_id'] ?? null;
    $questions = [];
    if ($selected_test_id) {
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC");
        $stmt->execute([$selected_test_id]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Usabio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Quick inline styles for demonstration */
        .action-forms {
            display: inline-block;
            margin-left: 10px;
        }
        .action-forms form {
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>
<body style="background:#f9f9f9;">
<div class="container mt-5">
    <h2>Usabio Admin</h2>
    <p><a href="?logout=1" class="btn btn-sm btn-secondary">Logout</a></p>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- CREATE NEW TEST -->
    <form method="post" class="mb-4">
        <h5>Create New Test</h5>
        <div class="form-row">
            <div class="col">
                <input type="text" name="test_title" class="form-control" placeholder="Test title" required>
            </div>
            <div class="col">
                <input type="text" name="test_description" class="form-control" placeholder="Description (optional)">
            </div>
            <div class="col-auto">
                <button type="submit" name="new_test" class="btn btn-primary">Create</button>
            </div>
        </div>
    </form>

    <!-- SELECT A TEST -->
    <form method="get" class="mb-4">
        <h5>Manage Questions for a Test</h5>
        <div class="form-row">
            <div class="col">
                <select name="test_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select a test --</option>
                    <?php foreach ($tests as $test): ?>
                        <option value="<?php echo $test['id'] ?>" <?php echo $selected_test_id == $test['id'] ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($test['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if ($selected_test_id) : ?>
        <!-- ADD NEW QUESTION -->
        <form method="post" class="form-inline mb-3">
            <input type="hidden" name="test_id" value="<?php echo $selected_test_id ?>">
            <input type="text" name="new_question" class="form-control mr-2" placeholder="New question" required>
            <button type="submit" class="btn btn-primary">Add Question</button>
        </form>

        <!-- LIST QUESTIONS -->
        <h5>Questions for Test ID <?php echo $selected_test_id ?>:</h5>
        <ul class="list-group">
            <?php foreach ($questions as $q): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#<?php echo $q['id'] ?>:</strong> <?php echo htmlspecialchars($q['text']) ?>
                    </div>
                    <div class="action-forms">
                        <!-- Edit form -->
                        <form method="post">
                            <input type="hidden" name="test_id" value="<?php echo $selected_test_id ?>">
                            <input type="hidden" name="question_id" value="<?php echo $q['id'] ?>">
                            <input type="hidden" name="action" value="edit_question">
                            <input type="text" name="updated_text" class="form-control form-control-sm" placeholder="New text...">
                            <button type="submit" class="btn btn-info btn-sm">Edit</button>
                        </form>

                        <!-- Delete form -->
                        <form method="post">
                            <input type="hidden" name="test_id" value="<?php echo $selected_test_id ?>">
                            <input type="hidden" name="question_id" value="<?php echo $q['id'] ?>">
                            <input type="hidden" name="action" value="delete_question">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>
