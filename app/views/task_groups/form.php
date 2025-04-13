<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <!-- Back Button -->
     <?php $context['test_id']; ?>
    <a href="/index.php?controller=Test&action=show&id=<?php echo $context['test_id']; ?>#taskgroup<?php echo $taskGroup['id']; ?>" class="btn btn-secondary btn-xs mb-4">
        ← Back to Test
    </a>  
   
    <h1><?php echo $taskGroup['id'] ? 'Edit' : 'Create'; ?> Task Group</h1>

    <form method="POST" action="/index.php?controller=TaskGroup&action=<?php echo $taskGroup['id'] ? 'update' : 'store'; ?>">
        <?php if ($taskGroup['id']) : ?>
            <input type="hidden" name="id" value="<?php echo $taskGroup['id']; ?>">
        <?php endif; ?>
        <input type="hidden" name="test_id" value="<?php echo $taskGroup['test_id']; ?>">

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($taskGroup['title']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" value="<?php echo htmlspecialchars($taskGroup['position']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <?php
        $anchor = $taskGroup['id'] ? '#taskgroup' . $taskGroup['id'] : '#task-group-list';
        $cancelUrl = '/index.php?controller=Test&action=show&id=' . $taskGroup['test_id'] . $anchor;
        ?>
        <a href="<?php echo $cancelUrl; ?>" class="btn btn-secondary">Cancel</a>

    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<!-- Bootstrap 5.3.1 JS Bundle -->

</body>
</html>
