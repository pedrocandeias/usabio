<?php 
$title = 'Add or edit test';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
<a href="/index.php?controller=Project&action=show&id=<?php echo $test['project_id']; ?>" class="btn btn-secondary mb-4">← Back to Project</a>

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
                            <a href="#" data-bs-toggle="modal" data-bs-target="#layoutImageModal">View Layout Image</a>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="layoutImageModal" tabindex="-1" aria-labelledby="layoutImageModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="layoutImageModalLabel">Layout Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="/uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" style="max-width: 100%; height: auto;" alt="Layout image">
                                    </div>
                                </div>
                            </div>
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
