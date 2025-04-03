<?php
session_start();
// Basic Admin Auth check or open listing, your choice
// For now, let's just show them to anyone; adapt as needed.

require_once '../config/db.php'; // or wherever your db.php is

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all tests
    $stmt = $pdo->query("SELECT * FROM tests ORDER BY id DESC");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usabio - Project List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#f9f9f9;">

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
            <li class="nav-item">
                <a href="logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>All Usability Projects</h2>
    <p>Select a project to manage.</p>

    <ul class="list-group">
    <?php foreach ($tests as $t): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong><?php echo htmlspecialchars($t['title']) ?></strong>
                <?php if (!empty($t['description'])): ?>
                    <br><small><?php echo htmlspecialchars($t['description']) ?></small>
                <?php endif; ?>
            </div>
            <div>
                <a href="manage-questions.php?test_id=<?php echo $t['id'] ?>" class="btn btn-primary btn-sm">
                    Manage
                </a>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>

</div>
</body>
</html>
