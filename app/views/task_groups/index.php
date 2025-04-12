<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
<?php if (!empty($test)) : ?>
    <p class="text-muted mb-3">
        <strong>Project:</strong> <?php echo htmlspecialchars($test['project_name']); ?><br>
        <strong>Test:</strong> <?php echo htmlspecialchars($test['title']); ?>
    </p>
<?php endif; ?>

    <h1 class="mb-4">Task Groups</h1>

    <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo htmlspecialchars($_GET['test_id']); ?>" class="btn btn-success mb-3">Add Task Group</a>

    <?php if (!empty($groups)) : ?>
        <ul class="list-group">
            <?php foreach ($groups as $group): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($group['title']); ?></strong>
                        <small class="text-muted"> (Position: <?php echo $group['position']; ?>)</small>
                    </div>
                    <div>
                        <a href="/index.php?controller=Task&action=create&group_id="<?php echo $group['id']; ?> class="btn btn-sm btn-outline-primary">Add Task</a>
                        <a href="/index.php?controller=TaskGroup&action=edit&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/index.php?controller=TaskGroup&action=destroy&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task group?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No task groups found.</p>
    <?php endif; ?>

    <a href="index.php?controller=Test&action=show&id=<?php echo htmlspecialchars($_GET['test_id']); ?>" class="btn btn-secondary mt-4">← Back to Project</a>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
