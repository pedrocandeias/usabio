<?php
// app/views/auth/login.php
// We'll assume $error is passed via the query string, e.g. ?error=Invalid%20credentials
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS (optional) -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    />
</head>
<body>
<div class="container py-5 form-outline mb-4">
<div class="row justify-content-center">
<div class="col-md-6">
    
    <div class="text-center mb-4">
        <img src="/assets/img/testflow-logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="/?controller=Auth&action=processLogin" method="POST">
        <div class="mb-3">
            <input
              type="text"
              class="form-control"
              name="username"
              id="username"
                placeholder="Enter your username"
              required
            />
        </div>
        <div class="mb-3">
            <input
              type="password"
              class="form-control"
              name="password"
              id="password"
              placeholder="Enter your password"
              required
            />
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
        </div>
    </form>
</div>
</body>
</html>
