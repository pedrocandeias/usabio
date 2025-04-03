<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = null;

    $selected_test_id = $_GET['test_id'] ?? null;
    if (!$selected_test_id) {
        die("No project selected.");
    }

    if (!$_SESSION['is_admin']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM moderator_test WHERE moderator_id = ? AND test_id = ?");
        $stmt->execute([$_SESSION['moderator_id'], $selected_test_id]);
        if ($stmt->fetchColumn() == 0) {
            die("You don't have permission for this project.");
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
    $stmt->execute([$selected_test_id]);
    $theProject = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$theProject) {
        die("Project not found. <a href='index.php'>Go back</a>.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_task']) && !empty($_POST['new_task'])) {
        $stmt = $pdo->prepare("INSERT INTO questions (test_id, text, scenario, script, question_type) VALUES (?, ?, ?, ?, 'task')");
        $stmt->execute(
            [
            $selected_test_id, 
            $_POST['new_task'], 
            $_POST['task_scenario'] ?? null,
            $_POST['task_script'] ?? null
            ]
        );
        $message = "Task added!";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['task_id'])) {
        $task_id = (int) $_POST['task_id'];

        if ($_POST['action'] === 'delete_task') {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND test_id = ? AND question_type = 'task'");
            $stmt->execute([$task_id, $selected_test_id]);
            $message = "Task #{$task_id} deleted!";
        }

        if ($_POST['action'] === 'edit_task' && !empty($_POST['updated_text'])) {
            $stmt = $pdo->prepare("UPDATE questions SET text = ?, scenario = ?, script = ? WHERE id = ? AND test_id = ? AND question_type = 'task'");
            $stmt->execute(
                [
                $_POST['updated_text'], 
                $_POST['updated_scenario'] ?? null,
                $_POST['updated_script'] ?? null,
                $task_id, 
                $selected_test_id
                ]
            );
            $message = "Task #{$task_id} updated!";
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
        $order = $_POST['order'] ?? [];

        // file_put_contents("debug_order.log", print_r($_POST, true)); // Log it

        foreach ($order as $position => $id) {
            $stmt = $pdo->prepare("UPDATE questions SET position = ? WHERE id = ? AND test_id = ?");
            $stmt->execute([$position, (int)$id, $selected_test_id]);
        }
        exit('success');
    }
    
    // $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? AND question_type = 'task' ORDER BY id ASC");
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? AND question_type = 'task' ORDER BY position ASC");
    $stmt->execute([$selected_test_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <h2>Manage Tasks for Project: <?php echo htmlspecialchars($theProject['title']) ?></h2>
    <p>
        <a href="projects.php" class="btn btn-sm btn-secondary">Back to Project</a>
    </p>
    
    <?php if ($message) : ?><div class="alert alert-success"><?php echo htmlspecialchars($message) ?></div><?php 
    endif; ?>

    <form method="post" class="mb-3">
        <input type="text" name="new_task" class="form-control mb-2" placeholder="Task name" required>
        <textarea name="task_script" class="form-control mb-2" placeholder="Task script (optional)"></textarea>
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>

    <ul class="list-group"  id="taskList">
        <?php foreach ($tasks as $task){ ?>
        <li class="list-group-item" data-task-id="<?php echo $task['id']; ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div> 
                    <strong>#<?php echo $task['id'] ?>:</strong> <?php echo htmlspecialchars($task['text']) ?><br>
                    <span class="font-weight-bold">Scenario:</span> <?php echo htmlspecialchars($task['scenario'] ?? '-') ?><br>
                    <span class="font-weight-bold">Script:</span> <?php echo htmlspecialchars($task['script'] ?? '-') ?><br>
                </div> 
                <div>
                    <button onclick="document.getElementById('edit<?php echo $task['id'] ?>').classList.toggle('hidden')" class="btn btn-info btn-sm">Edit</button>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="action" value="delete_task">
                        <input type="hidden" name="task_id" value="<?php echo $task['id'] ?>">
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
            <div class="hidden mt-3 mw-100" id="edit<?php echo $task['id'] ?>">
                <form method="post">
                    <input type="hidden" name="action" value="edit_task">
                    <input type="hidden" name="task_id" value="<?php echo $task['id'] ?>">
                    <label><strong>Name:</strong></label>
                    <input class="form-control mb-1" name="updated_text" value="<?php echo htmlspecialchars($task['text']) ?>">
                    <label><strong>Scenario:</strong></label>
                    <input type="text" class="form-control mb-1" name="updated_scenario" value="<?php echo htmlspecialchars($task['scenario'] ?? '') ?>" placeholder="Scenario (optional)">
                    <label><strong>Script:</strong></label>
                    <textarea class="form-control mb-1" name="updated_script"><?php echo htmlspecialchars($task['script']) ?></textarea>
                    <button class="btn btn-success btn-sm">Save</button>
                </form>
            </div>
        </li>
        <?php } ?>
    </ul>
    <button class="btn btn-success mt-2" id="saveOrder">Save Order</button>

</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
  const sortable = new Sortable(document.getElementById('taskList'), {
      animation: 150
  });

  document.getElementById('saveOrder').addEventListener('click', () => {
      const order = Array.from(document.querySelectorAll('#taskList li')).map(li => li.dataset.taskId);
      const formData = new FormData();
formData.append('update_order', '1');
order.forEach(id => formData.append('order[]', id));
fetch('', {
    method: 'POST',
    body: formData
})
.then(response => response.text())
.then(result => {
    if (result === 'success') alert('Order updated!');
    else alert('Error updating order: ' + result);
})
.catch(() => alert('Request failed.'));
  });
</script>

</body>
</html>
