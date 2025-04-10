<!-- app/views/projects/index.php -->


<?php 
$title = 'Projects';
require __DIR__ . '/../layouts/header.php'; ?>
<div class="container py-5">
        <h1 class="mb-4">Projects</h1>

        <p>
            <a href="/index.php?controller=Project&action=create" class="btn btn-success">Create New Project</a>
        </p>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $proj): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($proj['product_under_test']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($proj['business_case'])); ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="/index.php?controller=Project&action=edit&id=<?php echo $proj['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="/index.php?controller=Project&action=destroy&id=<?php echo $proj['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this project?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No projects found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>

<!-- Bootstrap 5.3.1 JS Bundle -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
></script>
</body>
</html>
