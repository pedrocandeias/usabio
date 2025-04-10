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
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                <?php echo $user['id'] ? 'New Password (leave blank to keep current)' : 'Password'; ?>
            </label>
            <input type="password" name="password" class="form-control" <?php echo $user['id'] ? '' : 'required'; ?>>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_admin" id="is_admin" class="form-check-input" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
            <label for="is_admin" class="form-check-label">Administrator</label>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="/index.php?controller=User&action=index" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>

<!-- Bootstrap 5.3.1 JS Bundle -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
></script>
</body>
</html>