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
                                <h5 class="card-title"><?php echo htmlspecialchars($proj['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($proj['description'])); ?></p>
                            </div>
                            <div class="card-footer d-flex flex-column gap-2">
                                 <a href="/index.php?controller=Project&action=show&id=<?php echo $proj['id']; ?>" class="btn btn-outline-secondary btn-sm w-100">View Project</a>
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
