<?php 
$title = 'Manage Tasks';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
<?php if (!empty($context)): ?>
    <p class="text-muted mb-4">
        <strong>Project:</strong> <?php echo htmlspecialchars($context['project_name']); ?><br>
        <strong>Test:</strong> <?php echo htmlspecialchars($context['test_title']); ?><br>
        <strong>Task Group:</strong> <?php echo htmlspecialchars($context['group_title']); ?>
    </p>
<?php endif; ?>

<h1 class="mb-4">Tasks for: <?php echo htmlspecialchars($testTitle); ?></h1>

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
    <a href="/index.php?controller=Project&action=show&id=<?php echo htmlspecialchars($projectId); ?>" class="btn btn-secondary mt-4">← Back to Project</a>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>

