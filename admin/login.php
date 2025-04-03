<?php
echo password_hash("testuser", PASSWORD_DEFAULT);

session_start();

// If the user is already logged in, redirect to admin area
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit;
}

require_once '../config/db.php'; // Adjust path as needed
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch the user from moderators
        $stmt = $pdo->prepare("SELECT * FROM moderators WHERE username = :uname LIMIT 1");
        $stmt->execute([':uname' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check password with password_verify
            if (password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['logged_in'] = true;
                $_SESSION['moderator_id'] = $user['id'];       // If you want to use the ID
                $_SESSION['moderator_username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin']; // store admin flag in session
                header("Location: projects.php");
                exit;
            } else {
                // Invalid password
                $message = "Invalid password.";
            }
        } else {
            // No such user
            $message = "Invalid user.";
        }
    } catch (PDOException $e) {
        $message = "DB error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login - Usabio</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#f9f9f9;">
<div class="container mt-5" style="max-width: 400px;">
    <h3>Usabio Login</h3>

    <?php if ($message) : ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input 
              type="text"
              class="form-control"
              name="username"
              id="username"
              placeholder="Enter your username"
              required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input 
              type="password"
              class="form-control"
              name="password"
              id="password"
              placeholder="Enter your password"
              required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
