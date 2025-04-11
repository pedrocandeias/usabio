<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $group['id'] ? 'Edit' : 'Create'; ?> Task Group</h1>

    <form method="POST" action="/index.php?controller=TaskGroup&action=<?php echo $group['id'] ? 'update' : 'store'; ?>">
        <?php if ($group['id']): ?>
            <input type="hidden" name="id" value="<?php echo $group['id']; ?>">
        <?php endif; ?>
        <input type="hidden" name="test_id" value="<?php echo $group['test_id']; ?>">

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($group['title']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" value="<?php echo htmlspecialchars($group['position']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="/index.php?controller=TaskGroup&action=index&test_id=<?php echo $group['test_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<!-- Bootstrap 5.3.1 JS Bundle -->

</body>
</html>
