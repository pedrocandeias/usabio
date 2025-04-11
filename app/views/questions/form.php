<?php 
$title = 'Questions for tests';
require __DIR__ . '/../layouts/header.php'; 
?>
<div class="container py-5">
    <h1><?php echo $question['id'] ? 'Edit' : 'Create'; ?> Question</h1>

    <form method="POST" action="/index.php?controller=Question&action=<?php echo $question['id'] ? 'update' : 'store'; ?>">
        <?php if ($question['id']): ?>
            <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
        <?php endif; ?>

        <input type="hidden" name="questionnaire_group_id" value="<?php echo $question['questionnaire_group_id']; ?>">
        <input type="hidden" name="test_id" value="<?php echo $_GET['test_id'] ?? $_POST['test_id'] ?? ''; ?>">

        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <textarea name="text" class="form-control" required><?php echo htmlspecialchars($question['text']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Question Type</label>
            <select name="question_type" class="form-select">
                <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                    <option value="<?php echo $type; ?>" <?php echo $question['question_type'] === $type ? 'selected' : ''; ?>>
                        <?php echo ucfirst($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Options (label:value, one per line)</label>
            <textarea name="question_options" class="form-control"><?php echo htmlspecialchars($question['question_options']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" value="<?php echo $question['position']; ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="/index.php?controller=Test&action=show&id=<?php echo $_GET['test_id'] ?? $_POST['test_id'] ?? ''; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>