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

    // CREATE a new test (automatically sets created_at)
    if (isset($_POST['new_test']) && !empty($_POST['test_title'])) {
        $testTitle = $_POST['test_title'];
        $testDescription = $_POST['test_description'] ?? '';
    
        // Step 1: Insert test without image first to get the ID
        $stmt = $pdo->prepare("INSERT INTO tests (title, description) VALUES (?, ?)");
        $stmt->execute([$testTitle, $testDescription]);
    
        $testId = $pdo->lastInsertId();
    
        $layoutImageName = null;
    
        // Step 2: If image was uploaded, process it
        if (isset($_FILES['layout_image']) && $_FILES['layout_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/layouts/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            $fileTmpPath = $_FILES['layout_image']['tmp_name'];
            $fileExt = pathinfo($_FILES['layout_image']['name'], PATHINFO_EXTENSION);
    
            // Step 3: Sanitize title for filename
            $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($testTitle));
            $newFileName = "layout_{$testId}_{$safeTitle}." . $fileExt;
            $destPath = $uploadDir . $newFileName;
    
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $layoutImageName = $newFileName;
    
                // Step 4: Update test row with the image filename
                $stmt = $pdo->prepare("UPDATE tests SET layout_image = ? WHERE id = ?");
                $stmt->execute([$layoutImageName, $testId]);
            }
        }
    
        $_SESSION['message'] = "New project created!";
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
    <title>Usabio Admin - New project</title>
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
    <h2>New project</h2>

    <?php if ($message) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message) ?></div>
    <?php } ?>

    <!-- Create Project Form -->
    <form method="post" class="mb-4" enctype="multipart/form-data">
        <h5>Create New Project</h5>
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <label for="test_title">Project Title:</label>
                    <input type="text" name="test_title" class="form-control" placeholder="Project title" required>
                </div>
                <div class="form-group">
                    <label for="test_description">Description:</label>
                    <textarea name="test_description" class="form-control" placeholder="Description (optional)" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="layout_image">Test layout:</label>
                    <input type="file" name="layout_image" id="layout_image" class="form-control-file" accept="image/*">
                </div>

                <button type="submit" name="new_test" class="btn btn-primary">Create</button>
           
            </div>
           
           
        </div>
    </form>
</div>

<?php require 'includes/footer.php'; ?>


</body>
</html>
