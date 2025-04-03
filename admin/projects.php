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
        $stmt = $pdo->prepare("INSERT INTO tests (title, description) VALUES (?, ?)");
        $stmt->execute([$_POST['test_title'], $_POST['test_description'] ?? '']);
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
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio Admin - <?php echo htmlspecialchars($theProject['title']) ?> / Manage Tasks</title>
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

    <?php if ($message) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message) ?></div>
    <?php } ?>

    <!-- Create Project Form -->
    <form method="post" class="mb-4">
        <h5>Create New Project</h5>
        <div class="form-row">
            <div class="col">
                <input type="text" name="test_title" class="form-control" placeholder="Project title" required>
            </div>
            <div class="col">
                <input type="text" name="test_description" class="form-control" placeholder="Description (optional)">
            </div>
            <div class="col-auto">
                <button type="submit" name="new_test" class="btn btn-primary">Create</button>
            </div>
        </div>
    </form>

    <!-- List Existing Projects -->
    <h5>Existing Projects</h5>
    <ul class="list-group">
        <?php foreach ($tests as $t): ?>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div>
                        <strong>#<?php echo $t['id'] ?>:</strong> 
                        <span id="title<?php echo $t['id'] ?>">
                            <?php echo htmlspecialchars($t['title']) ?>
                        </span>
                        <br>
                        <small>
                            Created on: <?php echo date("Y-m-d H:i", strtotime($t['created_at'])) ?>
                        </small>
                        <?php if (!empty($t['description'])) : ?>
                            <br><small><?php echo htmlspecialchars($t['description']) ?></small>
                        <?php endif; ?>
                    </div>
                    <div>
                        <!-- Results -->
                        <a href="view-results.php?test_id=<?php echo $t['id'] ?>" class="btn btn-sm btn-primary">Results</a>
                    </div>

                    <div>
                        <!-- Start test -->
                        <a href="start-test.php?test_id=<?php echo $t['id'] ?>" class="btn btn-lg btn-success">Start test</a>
                    </div>

                    <div>
                     
                        <!-- Manage Tasks -->
                        <a href="manage-tasks.php?test_id=<?php echo $t['id'] ?>" class="btn btn-sm btn-secondary">Manage Tasks</a>

                        <!-- Manage Questions -->
                        <a href="manage-questions.php?test_id=<?php echo $t['id'] ?>" class="btn btn-sm btn-info">Manage Questions</a>

                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" 
                                onclick="showEditForm(<?php echo $t['id'] ?>, '<?php echo htmlspecialchars($t['title'], ENT_QUOTES) ?>', '<?php echo htmlspecialchars($t['description'], ENT_QUOTES) ?>')">
                            Edit
                        </button>

                        <!-- Delete Form -->
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete project #<?php echo $t['id'] ?>?')">
                            <input type="hidden" name="delete_test_id" value="<?php echo $t['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Hidden Edit Form -->
                <div class="mt-2 hidden" id="editForm<?php echo $t['id'] ?>">
                    <form method="post" class="form-inline">
                        <input type="hidden" name="edit_test_id" value="<?php echo $t['id'] ?>">
                        <div class="form-group mb-2 mr-2">
                            <label for="editTestTitle<?php echo $t['id'] ?>" class="sr-only">Title</label>
                            <input type="text" class="form-control" name="edit_test_title" id="editTestTitle<?php echo $t['id'] ?>" required>
                        </div>
                        <div class="form-group mb-2 mr-2">
                            <label for="editTestDesc<?php echo $t['id'] ?>" class="sr-only">Description</label>
                            <input type="text" class="form-control" name="edit_test_description" id="editTestDesc<?php echo $t['id'] ?>">
                        </div>
                        <button type="submit" class="btn btn-success mb-2 mr-2">Save</button>
                        <button type="button" class="btn btn-secondary mb-2" onclick="hideEditForm(<?php echo $t['id'] ?>)">Cancel</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
function showEditForm(id, title, description) {
  document.getElementById('editForm' + id).classList.remove('hidden');
  document.getElementById('editTestTitle' + id).value = title;
  document.getElementById('editTestDesc' + id).value = description;
}

function hideEditForm(id) {
  document.getElementById('editForm' + id).classList.add('hidden');
}
</script>

</body>
</html>
