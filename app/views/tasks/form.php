<?php 
$title = $task['id'] ? 'Edit Task' : 'Create Task';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
<?php if (!empty($context)): ?>
    <a href="/index.php?controller=Test&action=show&id=<?php echo $context['test_id']; ?>#taskgroup<?php echo $task['task_group_id']; ?>" class="btn btn-secondary btn-xs mb-4">
        ← Back to Test
    </a>
<?php endif; ?>

    <h1 class="mb-4"><?php echo $title; ?></h1>

    <form method="POST" action="/index.php?controller=Task&action=<?php echo $task['id'] ? 'update' : 'store'; ?>">
        <?php if ($task['id']) : ?>
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
        <?php endif; ?>
        <input type="hidden" name="task_group_id" value="<?php echo $task['task_group_id']; ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Task Text</label>
                <textarea name="task_text" class="form-control" required rows="4"><?php echo htmlspecialchars($task['task_text']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Scenario</label>
                <textarea name="scenario" class="form-control" rows="4"><?php echo htmlspecialchars($task['scenario']); ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Script (what moderator should say)</label>
                <textarea name="script" class="form-control" rows="3"><?php echo htmlspecialchars($task['script']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Metrics (what to observe)</label>
                <textarea name="metrics" class="form-control" rows="3"><?php echo htmlspecialchars($task['metrics']); ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Type of Evaluation</label>
                <select name="task_type" id="task_type" class="form-select">
                    <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $task['task_type'] === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Predefined Evaluation Type (optional)</label>
                <select class="form-select" id="preset-options">
                    <option value="">— Select a common type —</option>
                    <option value="Completed:completed;Incompleted:incomplete">Completed/Incompleted</option>
                    <option value="Yes:yes;No:no">Yes / No</option>
                    <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1-5)</option>
                    <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1-5)</option>
                    <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1-5)</option>
                </select>
                <small class="form-text text-muted">Choosing one will auto-fill the options below.</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Evaluation Options</label>
            <textarea name="task_options" id="task_options" class="form-control" rows="3"><?php echo htmlspecialchars($task['task_options']); ?></textarea>
            <small class="form-text text-muted">Use the format: <code>Label:Value;Label:Value;</code></small>
        </div>

      
        <input type="hidden" name="position" class="form-control" value="<?php echo $task['position']; ?>">
      
        <div class="d-flex gap-2 mt-4">
            <input type="hidden" name="preset_type" id="preset_type" value="">
            <button type="submit" class="btn btn-primary">Save Task</button>
            <a href="/index.php?controller=Test&action=show&id=<?php echo $task['test_id']; ?>#taskgroup<?php echo $task['task_group_id']; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const presets = {
        "Completed/Incompleted": {
        type: "radio",
        options: "Completed:completed;Incompleted:incomplete"
    },
    "Yes / No": {
        type: "radio",
        options: "Yes:yes;No:no"
    },
    "Agreement Scale (1-5)": {
        type: "radio",
        options: "Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5"
    },
    "Difficulty Scale (1-5)": {
        type: "radio",
        options: "Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5"
    },
    "Satisfaction Scale (1-5)": {
        type: "radio",
        options: "Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5"
    }
};

document.getElementById('preset-options').addEventListener('change', function () {
    const selectedLabel = this.options[this.selectedIndex].text;
    const preset = presets[selectedLabel];

    if (preset) {
        document.getElementById('preset_type').value = selectedLabel;
        document.getElementById('task_options').value = preset.options;
        document.getElementById('task_type').value = preset.type;
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
