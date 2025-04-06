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
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']); // Remove it so it only shows once
    }

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
    
        $message = "New project created!";
    }

    // EDIT a test (do NOT update created_at)
    if (isset($_POST['edit_test_id'], $_POST['edit_test_title'])) {
        $edit_test_id = (int) $_POST['edit_test_id'];
        $edit_test_title = trim($_POST['edit_test_title']);
        $edit_test_desc  = trim($_POST['edit_test_description'] ?? '');

        $stmt = $pdo->prepare("UPDATE tests SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$edit_test_title, $edit_test_desc, $edit_test_id]);
        $message = "Project #{$edit_test_id} updated!";
    }

    // DELETE a test
    if (isset($_POST['delete_test_id'])) {
        $delete_test_id = (int)$_POST['delete_test_id'];
        $stmt = $pdo->prepare("DELETE FROM tests WHERE id = ? LIMIT 1");
        $stmt->execute([$delete_test_id]);
        $message = "Project #{$delete_test_id} deleted!";
    }

    // FETCH tests with created_at date
    if ($_SESSION['is_admin']) {
        $stmt = $pdo->query("SELECT * FROM tests ORDER BY id DESC");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare(
            "
            SELECT t.* FROM tests t
            JOIN moderator_test mt ON mt.test_id = t.id
            WHERE mt.moderator_id = :mod_id
            ORDER BY t.id DESC
        "
        );
        $stmt->execute(['mod_id' => $_SESSION['moderator_id']]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio Admin - Projects</title>
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
    <h2>Projects</h2>

    <a href="new-project.php" class="btn btn-primary mb-3">Create a new Project</a>
   
    <?php if ($message): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

   

    <!-- List Existing Projects -->
    <h5>Existing Projects</h5>
    <ul class="list-group">
        <?php foreach ($projects as $project): ?>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div>
                        <strong>#<?php echo $project['id'] ?>:</strong> 
                        <span id="title<?php echo $project['id'] ?>">
                            <?php echo htmlspecialchars($project['title']) ?>
                        </span>
                        <br>
                        <small>
                            Created on: <?php echo date("Y-m-d H:i", strtotime($project['created_at'])) ?>
                        </small>
                        <?php if (!empty($project['description'])) { ?>
                            <br><small><?php echo htmlspecialchars($project['description']) ?></small>
                        <?php } ?>
                        <?php if (!empty($project['layout_image'])) {?>
                            <br>
                            <a href="uploads/layouts/<?php echo htmlspecialchars($project['layout_image']) ?>" target="_blank"><img src="uploads/layouts/<?php echo htmlspecialchars($project['layout_image']) ?>" alt="Layout Image" class="img-thumbnail" style="max-width: 100px;"></a>
                        <?php } ?>
                    </div>
                 
                    <div>
                     
                        <!-- Manage Tasks -->
                        <a href="manage-tasks.php?test_id=<?php echo $project['id'] ?>" class="btn btn-sm btn-secondary">Manage Tasks</a>

                        <!-- Manage Questions -->
                        <a href="manage-questions.php?test_id=<?php echo $project['id'] ?>" class="btn btn-sm btn-info">Manage Questions</a>

                        <!-- Edit Button -->
                     
                        <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-warning">Edit</a>

                        <!-- Delete Form -->
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete project #<?php echo $project['id'] ?>?')">
                            <input type="hidden" name="delete_test_id" value="<?php echo $project['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>

                    </div>
                    <div>
                    <a href="view-results.php?test_id=<?php echo $project['id'] ?>" class="btn btn-lg btn-primary">Results</a>
               
                    <!-- Start test -->
                        <a href="start-test.php?test_id=<?php echo $project['id'] ?>" class="btn btn-lg btn-success">Start test</a>
                    </div>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
