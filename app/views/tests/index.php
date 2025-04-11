<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1>Tests for Project #<?php echo htmlspecialchars($_GET['project_id'] ?? ''); ?></h1>

    <a href="/index.php?controller=Test&action=create&project_id=<?php echo $_GET['project_id']; ?>" class="btn btn-success mb-3">Add Test</a>

    <?php if (!empty($test['layout_image'])): ?>
    <img src="/uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" style="max-width: 150px;" alt="Test Layout">
<?php endif; ?>


    <?php if (!empty($tests)): ?>
        <ul class="list-group">
            <?php foreach ($tests as $test): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?php echo htmlspecialchars($test['title']); ?></strong><br>
                        <small><?php echo htmlspecialchars($test['description']); ?></small>
                    </span>
                    <span>
                        <a href="/index.php?controller=Test&action=edit&id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="/index.php?controller=Test&action=destroy&id=<?php echo $test['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this test?');">Delete</a>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No tests found for this project.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
