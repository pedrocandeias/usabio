<?php $title = 'My Profile'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">My Profile</h1>

    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">✅ Profile updated successfully.</div>
    <?php endif; ?>

    <form method="POST" action="/index.php?controller=User&action=updateProfile">
        <div class="mb-3">
            <label class="form-label">Email (Username)</label>
            <input type="email" name="email" class="form-control disabled" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>

        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Company</label>
            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
        </div>

        <hr class="my-4">

        <h5>🔐 Change Password</h5>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control">
            <div class="form-text">Leave empty to keep current password</div>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
