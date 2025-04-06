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

    // Adicionar nova pergunta
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_question']) && !empty($_POST['new_question'])) {
        $question_type = $_POST['question_type'] ?? 'text';
        $question_options = $_POST['question_options'] ?? null;

        $stmt = $pdo->prepare("INSERT INTO questions (test_id, text, question_type, question_options) VALUES (?, ?, ?, ?)");
        $stmt->execute([$selected_test_id, $_POST['new_question'], $question_type, $question_options]);
        $message = "Question added!";
    }

    // Editar ou apagar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['question_id'])) {
        $question_id = (int) $_POST['question_id'];

        if ($_POST['action'] === 'delete_question') {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND test_id = ? LIMIT 1");
            $stmt->execute([$question_id, $selected_test_id]);
            $message = "Question #{$question_id} deleted!";
        }

        if ($_POST['action'] === 'edit_question' && !empty($_POST['updated_text'])) {
            $updated_text = trim($_POST['updated_text']);
            $updated_type = $_POST['updated_type'] ?? 'text';
            $updated_options = $_POST['updated_options'] ?? null;

            $stmt = $pdo->prepare("UPDATE questions SET text = ?, question_type = ?, question_options = ? WHERE id = ? AND test_id = ?");
            $stmt->execute([$updated_text, $updated_type, $updated_options, $question_id, $selected_test_id]);
            $message = "Question #{$question_id} updated!";
        }
    }

    // Listar perguntas
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC");
    $stmt->execute([$selected_test_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio Admin - <?php echo htmlspecialchars($theProject['title']) ?> / Manage Questions</title>
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
            <li><a class="nav-link disabled" href="#">Logged in as: <?php echo htmlspecialchars($_SESSION['moderator_username']) ?></a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Manage Questions for Project: <?php echo htmlspecialchars($theProject['title']) ?></h2>
    <p><a href="index.php" class="btn btn-sm btn-secondary">Back to Project List</a></p>

    <?php if ($message) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Question -->
    <form method="post" class="mb-3">
        <div class="form-group">
            <select name="question_type" id="question_type" class="form-control mb-2" onchange="toggleOptionsInput()">
                <option value="text">Text</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
                <option value="select">Dropdown</option>
            </select>    
            <textarea name="new_question" class="form-control mb-2" placeholder="New question" rows="3" required></textarea>
            <input type="text" name="question_options" id="question_options"
                   class="form-control mb-2" placeholder="Options (label:value;...)" style="display:none;">
        </div>
        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>

    <!-- List Questions -->
    <ul class="list-group" id="taskList">
        <?php foreach ($questions as $q): ?>
            <?php $qid = $q['id']; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center" data-task-id="<?php echo $qid; ?>">
                <div>
                    <strong>#<?php echo $qid ?>:</strong> <?php echo htmlspecialchars($q['text']) ?>
                    <div class="text-muted small">
                        Type: <em><?php echo htmlspecialchars($q['question_type']) ?></em>
                        <?php if (!empty($q['question_options'])) : ?>
                            <br>Options: <?php echo htmlspecialchars($q['question_options']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-info btn-sm"
                            onclick="showEditForm(<?php echo $qid ?>,
                                '<?php echo htmlspecialchars($q['text'], ENT_QUOTES) ?>',
                                '<?php echo htmlspecialchars($q['question_type'], ENT_QUOTES) ?>',
                                '<?php echo htmlspecialchars($q['question_options'], ENT_QUOTES) ?>')">
                        Edit
                    </button>
                    <form method="post" class="d-inline" onsubmit="return confirm('Delete question #<?php echo $qid ?>?')">
                        <input type="hidden" name="action" value="delete_question">
                        <input type="hidden" name="question_id" value="<?php echo $qid ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </li>

            <!-- Hidden Edit Form -->
            <li class="list-group-item hidden" id="editForm<?php echo $qid ?>">
                <form method="post" class="form">
                    <input type="hidden" name="action" value="edit_question">
                    <input type="hidden" name="question_id" value="<?php echo $qid ?>">

                    <div class="form-group mb-2">
                        <textarea name="updated_text" id="updatedText<?php echo $qid ?>" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <select name="updated_type" id="updatedType<?php echo $qid ?>" class="form-control" onchange="toggleEditOptions(<?php echo $qid ?>)">
                            <option value="text">Text</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="select">Dropdown</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <input type="text" name="updated_options" id="updatedOptions<?php echo $qid ?>"
                               class="form-control" placeholder="Options (label:value;...)" style="display:none;">
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="hideEditForm(<?php echo $qid ?>)">Cancel</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <button class="btn btn-success mt-2" id="saveOrder">Save Order</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function toggleOptionsInput() {
    const type = document.getElementById("question_type").value;
    const options = document.getElementById("question_options");
    options.style.display = (["radio", "checkbox", "select"].includes(type)) ? "block" : "none";
}
function toggleEditOptions(id) {
    const type = document.getElementById("updatedType" + id).value;
    const options = document.getElementById("updatedOptions" + id);
    options.style.display = (["radio", "checkbox", "select"].includes(type)) ? "block" : "none";
}
function showEditForm(id, text, type, options) {
    document.getElementById('updatedText' + id).value = text;
    document.getElementById('updatedType' + id).value = type;
    document.getElementById('updatedOptions' + id).value = options;
    toggleEditOptions(id);
    document.getElementById('editForm' + id).classList.remove('hidden');
}
function hideEditForm(id) {
    document.getElementById('editForm' + id).classList.add('hidden');
}
document.addEventListener("DOMContentLoaded", toggleOptionsInput);

const sortable = new Sortable(document.getElementById('taskList'), { animation: 150 });
document.getElementById('saveOrder').addEventListener('click', () => {
    const order = Array.from(document.querySelectorAll('#taskList li[data-task-id]')).map(li => li.dataset.taskId);
    const formData = new FormData();
    formData.append('update_order', '1');
    order.forEach(id => formData.append('order[]', id));
    fetch('', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(res => alert(res === 'success' ? 'Order updated!' : 'Error: ' + res))
        .catch(() => alert('Request failed.'));
});
</script>
</body>
</html>
