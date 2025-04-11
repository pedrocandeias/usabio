<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1><?php echo $task['id'] ? 'Edit' : 'Create'; ?> Task</h1>

    <form method="POST" action="/index.php?controller=Task&action=<?php echo $task['id'] ? 'update' : 'store'; ?>">
        <?php if ($task['id']): ?>
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
        <?php endif; ?>
        <input type="hidden" name="task_group_id" value="<?php echo $task['task_group_id']; ?>">

        <div class="mb-3">
            <label class="form-label">Task Text</label>
            <textarea name="task_text" class="form-control" required><?php echo htmlspecialchars($task['task_text']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Script</label>
            <textarea name="script" class="form-control"><?php echo htmlspecialchars($task['script']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Scenario</label>
            <textarea name="scenario" class="form-control"><?php echo htmlspecialchars($task['scenario']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Metrics</label>
            <textarea name="metrics" class="form-control"><?php echo htmlspecialchars($task['metrics']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Task Type</label>
            <select name="task_type" class="form-select">
                <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                    <option value="<?php echo $type; ?>" <?php echo $task['task_type'] === $type ? 'selected' : ''; ?>>
                        <?php echo ucfirst($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Task Options (label:value, one per line)</label>
            <textarea name="task_options" class="form-control"><?php echo htmlspecialchars($task['task_options']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" value="<?php echo $task['position']; ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save Task</button>
        <a href="/index.php?controller=Task&action=index&group_id=<?php echo $task['task_group_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>

