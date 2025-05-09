<?php require __DIR__ . '/../layouts/head.php'; ?>

<body class="auth-bg">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column justify-content-center align-items-center min-vh-100">
            <div class="card p-10 shadow-sm">
                <h2 class="mb-5">Email Confirmation</h2>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="/index.php?controller=Auth&action=login" class="btn btn-primary mt-4">Go to Login</a>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
