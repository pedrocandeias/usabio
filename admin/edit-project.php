<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = null;

    // Get test ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid test ID.");
    }

    $testId = (int)$_GET['id'];

    // Fetch test info
    $stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
    $stmt->execute([$testId]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        die("Test not found.");
    }

    // Handle update
    if (isset($_POST['update_test']) && !empty($_POST['test_title'])) {
        $testTitle = $_POST['test_title'];
        $testDescription = $_POST['test_description'] ?? '';
        $layoutImageName = $test['layout_image'];

        if (isset($_FILES['layout_image']) && $_FILES['layout_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/layouts/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileTmpPath = $_FILES['layout_image']['tmp_name'];
            $fileExt = pathinfo($_FILES['layout_image']['name'], PATHINFO_EXTENSION);
            $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($testTitle));
            $newFileName = "layout_{$testId}_{$safeTitle}." . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $layoutImageName = $newFileName;
            }
        }

        // Update test
        $stmt = $pdo->prepare("UPDATE tests SET title = ?, description = ?, layout_image = ? WHERE id = ?");
        $stmt->execute([$testTitle, $testDescription, $layoutImageName, $testId]);

        $_SESSION['message'] = "Project updated!";
        header("Location: projects.php");
        exit;
    }

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio Admin - Edit Project</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>

<!-- Simple Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="projects.php">Usabio Admin</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="projects.php">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">Users</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li>
                <a class="nav-link disabled" href="#">Logged in as:
            <?php echo htmlspecialchars($_SESSION['moderator_username']) ?></a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Edit Project</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="test_title">Project Title:</label>
            <input type="text" name="test_title" class="form-control" value="<?php echo htmlspecialchars($test['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="test_description">Description:</label>
            <textarea name="test_description" class="form-control" rows="3"><?php echo htmlspecialchars($test['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="layout_image">Test Layout (optional):</label><br>
            <?php if (!empty($test['layout_image'])): ?>
                <img src="uploads/layouts/<?php echo htmlspecialchars($test['layout_image']); ?>" style="max-width: 300px; display: block; margin-bottom: 10px;">
            <?php endif; ?>
            <input type="file" name="layout_image" id="layout_image" class="form-control-file" accept="image/*">
        </div>
        <button type="submit" name="update_test" class="btn btn-success">Update Project</button>
        <a href="projects.php" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
