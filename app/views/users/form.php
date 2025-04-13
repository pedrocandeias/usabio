<?php 
$title = 'User Management';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $user['id'] ? 'Edit' : 'Create'; ?> User</h1>

    <form method="POST" action="/index.php?controller=User&action=<?php echo $user['id'] ? 'update' : 'store'; ?>">
    <?php if ($user['id']): ?>
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Company</label>
        <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="isAdmin"
            <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="isAdmin">Administrator</label>
    </div>

    <button type="submit" class="btn btn-primary">Save User</button>
    <a href="/index.php?controller=User&action=index" class="btn btn-secondary">Cancel</a>
</form>

</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?></body>
</html>