<?php 
$title = 'Add or edit test';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
<a href="/index.php?controller=Project&action=show&id=<?php echo $test['project_id']; ?>" class="btn btn-secondary mb-4">← Back to Project</a>
<?php if (!empty($context)): ?>
    <p class="text-muted mb-4">
        <strong>Project:</strong> <?php echo htmlspecialchars($context['product_under_test']); ?>
    </p>
<?php endif; ?>

    <h1><?php echo $test['id'] ? 'Edit' : 'Create'; ?> Test</h1>

    <form method="POST" enctype="multipart/form-data" action="/index.php?controller=Test&action=<?php echo $test['id'] ? 'update' : 'store'; ?>">
        <input type="hidden" name="project_id" value="<?php echo $test['project_id']; ?>">
        <?php if ($test['id']): ?>
            <input type="hidden" name="id" value="<?php echo $test['id']; ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-7">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($test['title']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($test['description']); ?></textarea>
                </div>
            </div>

            <div class="col-md-5">
                <div class="mb-3">
                    <label class="form-label">Layout Image</label>
                    <input type="file" name="layout_image" class="form-control" accept="image/*">
                    <?php if (!empty($test['layout_image'])): ?>
                        <div class="mt-2">
                            <img src="/uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" style="max-width: 100%; height: auto;" alt="Layout image">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Test</button>
            <a href="/index.php?controller=Project&action=show&id=<?php echo $test['project_id']; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
