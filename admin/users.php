<?php
session_start();

// If user wants to log out via ?logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php"); // or wherever
    exit;
}

// Check session-based login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // If not logged in, redirect to your multi-user login page
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

if (!$_SESSION['is_admin']) {
    die("You don't have permission to access this page.");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = null;

    // Fetch all projects
    $stmt = $pdo->query("SELECT * FROM tests ORDER BY id DESC");
    $allProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CREATE new moderator
    if (isset($_POST['action']) && $_POST['action'] === 'create_user') {
        $user  = trim($_POST['username']);
        $pass  = trim($_POST['password']);

        // Hash the password
        $passHash = password_hash($pass, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO moderators (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$user, $passHash]);
        $newUserId = $pdo->lastInsertId();

        // Assign to selected projects
        $assignedProjects = $_POST['projects'] ?? []; // array of test_ids
        foreach ($assignedProjects as $testId) {
            $stmt = $pdo->prepare("INSERT INTO moderator_test (moderator_id, test_id) VALUES (?, ?)");
            $stmt->execute([$newUserId, $testId]);
        }

        $message = "User '$user' created successfully!";
    }

    // EDIT existing moderator
    if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
        $modId = (int) $_POST['user_id'];
        $newUsername = trim($_POST['username']);
        $newPassword = trim($_POST['password'] ?? '');

        // Update username
        $sql = "UPDATE moderators SET username = :uname WHERE id = :uid LIMIT 1";
        $updateParams = [':uname' => $newUsername, ':uid' => $modId];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateParams);

        // If new password was provided, update the hash
        if ($newPassword !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE moderators SET password_hash = ? WHERE id = ? LIMIT 1");
            $stmt->execute([$hash, $modId]);
        }

        // Now handle project assignments
        // 1. Remove all existing links
        $stmt = $pdo->prepare("DELETE FROM moderator_test WHERE moderator_id = ?");
        $stmt->execute([$modId]);

        // 2. Add links for newly selected projects
        $assignedProjects = $_POST['projects'] ?? [];
        foreach ($assignedProjects as $testId) {
            $stmt = $pdo->prepare("INSERT INTO moderator_test (moderator_id, test_id) VALUES (?, ?)");
            $stmt->execute([$modId, $testId]);
        }

        $message = "User #$modId updated successfully!";
    }

    // DELETE moderator
    if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
        $modId = (int)$_POST['user_id'];
        // This will cascade in 'moderator_test' if you have foreign keys, or we do it manually:
        $stmt = $pdo->prepare("DELETE FROM moderators WHERE id = ? LIMIT 1");
        $stmt->execute([$modId]);
        $message = "User #$modId removed!";
    }

    // Fetch all moderators
    $sql = "SELECT m.id, m.username FROM moderators m ORDER BY m.id ASC";
    $stmt = $pdo->query($sql);
    $moderators = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each moderator, fetch assigned projects
    // We'll build an array like: $userProjects[moderator_id] = array of test_ids
    $userProjects = [];
    foreach ($moderators as $m) {
        $mId = $m['id'];
        $stmtLink = $pdo->prepare("SELECT test_id FROM moderator_test WHERE moderator_id = ?");
        $stmtLink->execute([$mId]);
        $projIds = $stmtLink->fetchAll(PDO::FETCH_COLUMN);
        $userProjects[$mId] = $projIds;
    }

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio Admin - Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body style="background:#f9f9f9;">

<!-- Navbar -->
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
            <li class="nav-item">
                <a href="logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Users (Moderators)</h2>

    <?php if ($message) : ?>
        <div class="alert alert-success"><?php htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- CREATE NEW USER -->
    <form method="post" class="mb-4">
        <h5>Create New User</h5>
        <input type="hidden" name="action" value="create_user">
        <div class="form-row mb-2">
            <div class="col">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="col">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
        </div>
        <label>Assign Projects:</label><br>
        <div class="form-group form-check form-check-inline">
            <?php foreach ($allProjects as $proj): ?>
                <label class="form-check-label mr-2">
                    <input type="checkbox" class="form-check-input" name="projects[]" value="<?php echo $proj['id'] ?>">
                    <?php echo htmlspecialchars($proj['title']) ?>
                </label>
            <?php endforeach; ?>
        </div><br>
        <button type="submit" class="btn btn-primary">Add User</button>
    </form>

    <!-- LIST ALL USERS -->
    <h5>Existing Users</h5>
    <ul class="list-group">
        <?php foreach ($moderators as $mod): ?>
            <li class="list-group-item">
                <?php $mId = $mod['id']; ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>#<?php echo $mId ?>:</strong> <?php echo htmlspecialchars($mod['username']) ?>
                        <br>
                        <!-- Show assigned projects -->
                        <small>
                            Projects:
                            <?php
                            if (!empty($userProjects[$mId])) {
                                $projTitles = [];
                                foreach ($userProjects[$mId] as $pID) {
                                    // find the project title
                                    foreach ($allProjects as $p) {
                                        if ($p['id'] == $pID) {
                                            $projTitles[] = $p['title'];
                                            break;
                                        }
                                    }
                                }
                                echo implode(', ', $projTitles);
                            } else {
                                echo "None";
                            }
                            ?>
                        </small>
                    </div>
                    <div>
                        <!-- Edit button toggles hidden form -->
                        <button type="button" class="btn btn-sm btn-info"
                                onclick="showEditForm(<?php echo $mId ?>, '<?php echo htmlspecialchars($mod['username'], ENT_QUOTES) ?>')">
                            Edit
                        </button>

                        <!-- Delete form -->
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete user #<?php echo $mId ?>?')">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo $mId ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Hidden Edit Form -->
                <div class="mt-2 hidden" id="editForm<?php echo $mId ?>">
                    <form method="post">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" value="<?php echo $mId ?>">
                        <div class="form-row mb-2">
                            <div class="col">
                                <label>Username</label>
                                <input type="text" name="username" id="usernameField<?php echo $mId ?>"
                                       class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Update Password (optional)</label>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                        <label>Assign Projects:</label><br>
                        <div class="form-group form-check form-check-inline">
                            <?php foreach ($allProjects as $proj): ?>
                                <?php $checked = in_array($proj['id'], $userProjects[$mId] ?? []) ? 'checked' : ''; ?>
                                <label class="form-check-label mr-2">
                                    <input type="checkbox" class="form-check-input" name="projects[]" value="<?php echo $proj['id'] ?>" <?php echo $checked ?>>
                                    <?php echo htmlspecialchars($proj['title']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div><br>
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" onclick="hideEditForm(<?php echo $mId ?>)">Cancel</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
function showEditForm(userId, currentUsername) {
    document.getElementById('editForm' + userId).classList.remove('hidden');
    document.getElementById('usernameField' + userId).value = currentUsername;
}

function hideEditForm(userId) {
    document.getElementById('editForm' + userId).classList.add('hidden');
}
</script>

</body>
</html>
