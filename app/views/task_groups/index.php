<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Task Groups</h1>

    <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo htmlspecialchars($_GET['test_id']); ?>" class="btn btn-success mb-3">Add Task Group</a>

    <?php if (!empty($groups)): ?>
        <ul class="list-group">
            <?php foreach ($groups as $group): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($group['title']); ?></strong>
                        <small class="text-muted"> (Position: <?php echo $group['position']; ?>)</small>
                    </div>
                    <div>
                        <a href="/index.php?controller=TaskGroup&action=edit&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/index.php?controller=TaskGroup&action=destroy&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task group?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No task groups found.</p>
    <?php endif; ?>

    <a href="/index.php?controller=Test&action=edit&id=<?php echo htmlspecialchars($_GET['test_id']); ?>" class="btn btn-secondary mt-4">← Back to Test</a>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
