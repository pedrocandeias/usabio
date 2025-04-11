<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Tasks</h1>

    <a href="/index.php?controller=Task&action=create&group_id=<?php echo htmlspecialchars($_GET['group_id']); ?>" class="btn btn-success mb-3">Add Task</a>

    <?php if (!empty($tasks)): ?>
        <ul class="list-group">
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row">
                    <div class="mb-2 mb-md-0">
                        <strong><?php echo htmlspecialchars($task['task_text']); ?></strong><br>
                        <small class="text-muted">Type: <?php echo $task['task_type']; ?>, Pos: <?php echo $task['position']; ?></small>
                    </div>
                    <div>
                        <a href="/index.php?controller=Task&action=edit&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/index.php?controller=Task&action=destroy&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No tasks found in this group.</p>
    <?php endif; ?>

    <a href="/index.php?controller=TaskGroup&action=index&test_id=<?php echo htmlspecialchars($_GET['test_id'] ?? ''); ?>" class="btn btn-secondary mt-4">← Back to Task Groups</a>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>

