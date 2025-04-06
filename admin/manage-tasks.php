<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

function renderEvaluationTypeDropdown($selected, $prefix = 'updated_')
{
    echo '<label><strong>Evaluation Type:</strong></label>';
    echo '<select name="' . $prefix . 'task_type" class="form-control mb-1">';
    echo '<option value="text"' . ($selected === 'text' ? ' selected' : '') . '>Text Input</option>';
    echo '<option value="radio"' . ($selected === 'radio' ? ' selected' : '') . '>Radio Buttons</option>';
    echo '<option value="checkbox"' . ($selected === 'checkbox' ? ' selected' : '') . '>Checkboxes</option>';
    echo '</select>';
}

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
        //$stmt = $pdo->prepare("INSERT INTO tasks (test_id, text, scenario, script, metrics, task_type, task_options, evaluation_errors, task_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'task')");
        $stmt = $pdo->prepare("INSERT INTO tasks (test_id, task_text, scenario, script, metrics, task_type, task_options) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if($_POST['task_type'] === 'text') {
            $task_options = $_POST['task_options_text'] ?? '';
        } elseif($_POST['task_type'] === 'radio') {
            $task_options = $_POST['task_options_radio'] ?? '';
        } elseif($_POST['task_type'] === 'checkbox') {
            $task_options = $_POST['task_options_checkbox'] ?? '';
        } else {
            $task_options = $_POST['task_options_text'] ?? '';
        }
        
        $stmt->execute(
            [
            $selected_test_id,
            $_POST['new_task'],
            $_POST['task_scenario'] ?? null,
            $_POST['task_script'] ?? null,
            $_POST['task_metrics'] ?? null,
            $_POST['task_type'] ?? 'text',
            $task_options,
            ]
        );
        $message = "Task added!";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['task_id'])) {
        $task_id = (int) $_POST['task_id'];

        if ($_POST['action'] === 'delete_task') {
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND test_id = ? AND task_type = 'task'");
            $stmt->execute([$task_id, $selected_test_id]);
            $message = "Task #{$task_id} deleted!";
        }

        if ($_POST['action'] === 'edit_task' && !empty($_POST['updated_text'])) {
            $task_type = $_POST['updated_task_type'] ?? 'text';
            if($task_type === 'text') {
                $task_options = $_POST['updated_task_options_text'] ?? '';
            } elseif($task_type === 'radio') {
                $task_options = $_POST['updated_task_options_radio'] ?? '';
            } elseif($task_type === 'checkbox') {
                $task_options = $_POST['updated_task_options_checkbox'] ?? '';
            } else {
                $task_options = $_POST['updated_task_options_text'] ?? '';
            }

            $stmt = $pdo->prepare("UPDATE tasks SET task_text = ?, scenario = ?, script = ?, metrics = ?, task_type = ?, task_options = ? WHERE id = ? AND test_id = ?");
            $stmt->execute(
                [
                    $_POST['updated_text'],
                    $_POST['updated_scenario'] ?? null,
                    $_POST['updated_script'] ?? null,
                    $_POST['updated_metrics'] ?? null,
                    $task_type,
                    $task_options,
                    $task_id,
                    $selected_test_id
                ]
            );
            $message = "Task #{$task_id} updated!";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
        $order = $_POST['order'] ?? [];
        foreach ($order as $position => $id) {
            $stmt = $pdo->prepare("UPDATE tasks SET position = ? WHERE id = ? AND test_id = ?");
            $stmt->execute([$position, (int)$id, $selected_test_id]);
        }
        exit('success');
    }

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE test_id = ? ORDER BY position ASC");
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

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="projects.php">Usabio Admin</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="projects.php">Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        </ul>
        <ul class="navbar-nav">
            <li><a class="nav-link disabled">Logged in as: <?php echo htmlspecialchars($_SESSION['moderator_username']) ?></a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Manage Tasks for Project: <?php echo htmlspecialchars($theProject['title']) ?></h2>
    <p><a href="projects.php" class="btn btn-sm btn-secondary">Back to Projects</a></p>
    <?php if ($message) : ?><div class="alert alert-success"><?php echo htmlspecialchars($message) ?></div><?php 
    endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h4>Add New Task</h4>
            <form method="post" class="mb-3">
                <input type="text" name="new_task" class="form-control mb-2" placeholder="Task name" required>
                <textarea name="task_scenario" class="form-control mb-2" placeholder="Scenario"></textarea>
                <textarea name="task_script" class="form-control mb-2" placeholder="Script"></textarea>
                <textarea name="task_metrics" class="form-control mb-2" placeholder="Metrics"></textarea>

                <label><strong>Evaluation Type:</strong></label>
                <select name="task_type" id="task_type" class="form-control mb-2">
                    <option value="text">Text Input</option>
                    <option value="radio">Radio Buttons</option>
                    <option value="checkbox">Checkboxes</option>
                </select>

                <div id="TaskTextInputSection" class="mb-2">
                    <label>Text Input:</label>
                    <textarea name="task_options_text" class="form-control" placeholder="e.g. Error message"></textarea>
                </div>
                <div id="TaskCheckboxSection" class="mb-2 hidden">
                    <label>Options (label:value; e.g. yes:1;no:0;):</label>
                    <textarea name="task_options_checkbox" class="form-control" placeholder="e.g. label:value;"></textarea>
                </div>
                <div id="TaskRadioSection" class="mb-2 hidden">
                    <label>Options (label:value; e.g. Very good:2;Good:1;Bad:-1;Very bad:-2;):</label>
                    <textarea name="task_options_radio" class="form-control" placeholder="e.g. label:value;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Task</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Existing Tasks</h4>
            <ul class="list-group" id="taskList">
                <?php foreach ($tasks as $task) { ?>
                <li class="list-group-item" data-task-id="<?php echo $task['id']; ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>#<?php echo $task['id'] ?>:</strong> <?php echo htmlspecialchars($task['task_text']) ?><br>
                            <span class="font-weight-bold">Scenario:</span> <?php echo htmlspecialchars($task['scenario'] ?? '-') ?><br>
                            <span class="font-weight-bold">Script:</span> <?php echo htmlspecialchars($task['script'] ?? '-') ?><br>
                            <span class="font-weight-bold">Metrics:</span> <?php echo htmlspecialchars($task['metrics'] ?? '-') ?><br>
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
                            <input class="form-control mb-1" name="updated_text" value="<?php echo htmlspecialchars($task['task_text']) ?>">
                            <label><strong>Scenario:</strong></label>
                            <input type="text" class="form-control mb-1" name="updated_scenario" value="<?php echo htmlspecialchars($task['scenario'] ?? '') ?>">
                            <label><strong>Script:</strong></label>
                            <textarea class="form-control mb-1" name="updated_script"><?php echo htmlspecialchars($task['script']) ?></textarea>
                            <label><strong>Metrics:</strong></label>
                            <textarea class="form-control mb-1" name="updated_metrics"><?php echo htmlspecialchars($task['metrics'] ?? '') ?></textarea>
                            <label><strong>Evaluation Type:</strong></label>
                            <select name="updated_task_type" class="form-control mb-1" onchange="toggleEditEvalFields(this, <?php echo $task['id'] ?>)">
                                <option value="text" <?php echo $task['task_type'] === 'text' ? 'selected' : '' ?>>Text Input</option>
                                <option value="radio" <?php echo $task['task_type'] === 'radio' ? 'selected' : '' ?>>Radio Buttons</option>
                                <option value="checkbox" <?php echo $task['task_type'] === 'checkbox' ? 'selected' : '' ?>>Checkboxes</option>
                            </select>
                            <div class="mb-2" id="TaskEditTextInputSection<?php echo $task['id'] ?>" style="<?php echo $task['task_type'] === 'text' ? '' : 'display:none;' ?>">
                                <label><strong>Text Options:</strong></label>
                                <textarea class="form-control" name="updated_task_options_text" value="<?php echo htmlspecialchars($task['task_options'] ?? '') ?>"><?php echo htmlspecialchars($task['task_options'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-2" id="TaskEditRadioSection<?php echo $task['id'] ?>" style="<?php echo $task['task_type'] === 'radio' ? '' : 'display:none;' ?>">
                               <label><strong>Radio buttons Options:</strong></label>
                               <input type="text" class="form-control mb-1" name="updated_task_options_radio" value="<?php echo htmlspecialchars($task['task_options'] ?? '') ?>">
                            </div>
                            <div class="mb-2" id="TaskEditCheckboxSection<?php echo $task['id'] ?>" style="<?php echo $task['task_type'] === 'checkbox' ? '' : 'display:none;' ?>">
                                <label><strong>Checkboxes Options:</strong></label>
                                <textarea class="form-control" name="updated_task_options_checkbox" value="<?php echo htmlspecialchars($task['task_options'] ?? '') ?>"><?php echo htmlspecialchars($task['task_options'] ?? '') ?></textarea>
                            </div>
                            <button class="btn btn-success btn-sm">Save</button>
                        </form>
                    </div>
                </li>
                <?php } ?>
            </ul>
            <button class="btn btn-success mt-2" id="saveOrder">Save Order</button>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>

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


document.getElementById('task_type').addEventListener('change', function () {
    const type = this.value;
    document.getElementById('TaskTextInputSection').style.display = type === 'text' ? 'block' : 'none';
    document.getElementById('TaskRadioSection').style.display = type === 'radio' ? 'block' : 'none';
    document.getElementById('TaskCheckboxSection').style.display = type === 'checkbox' ? 'block' : 'none';
});

function toggleEditEvalFields(selectEl, taskId) {
    const type = selectEl.value;
    document.getElementById('TaskEditTextInputSection' + taskId).style.display = type === 'text' ? 'block' : 'none';
    document.getElementById('TaskEditRadioSection' + taskId).style.display = type === 'radio' ? 'block' : 'none';
    document.getElementById('TaskEditCheckboxSection' + taskId).style.display = type === 'checkbox' ? 'block' : 'none';
}
</script>

</body>
</html>
