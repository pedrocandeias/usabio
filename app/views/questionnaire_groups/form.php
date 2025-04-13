<?php 
$title = 'Questions for tests';
require __DIR__ . '/../layouts/header.php'; 
?>
<div class="container py-5">

<?php if (!empty($context)): ?>
    <a href="/index.php?controller=Test&action=show&id=<?php echo $context['test_id']; ?>#questionnaire-group<?php echo $group['id']; ?>" class="btn btn-secondary btn-xs mb-4">
        ← Back to Test
    </a>
<?php endif; ?>

    <h1 class="mb-4"><?php echo $group['id'] ? 'Edit' : 'Create'; ?> Questionnaire Group</h1>

    <form method="POST" action="/index.php?controller=QuestionnaireGroup&action=<?php echo $group['id'] ? 'update' : 'store'; ?>">
        <?php if ($group['id']) : ?>
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
        <?php
        $anchor = $group['id'] ? '#questionnaire-group' . $group['id'] : '#questionnaire-group-list';
        $cancelUrl = '/index.php?controller=Test&action=show&id=' . $group['test_id'] . $anchor;
        ?>
        <a href="<?php echo $cancelUrl; ?>" class="btn btn-secondary">Cancel</a>

    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
