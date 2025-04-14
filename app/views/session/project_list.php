<?php $title = 'Choose Project'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Select a Project</h1>

    <?php if (!empty($projects)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="text-muted mb-2"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                            <p class="mb-0">
                                📅 Created: <?php echo date('Y-m-d', strtotime($project['created_at'])); ?>
                            </p>
                        </div>
                        <div class="card-footer text-end">
                            <a href="/index.php?controller=Session&action=dashboard&project_id=<?php echo $project['id']; ?>"
                               class="btn btn-outline-primary btn-sm w-100">
                                ▶ Start Test Sessions
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No projects found or assigned to you.</p>
        <a href="/index.php?controller=Project&action=create" class="btn btn-primary">
            Create a New Project
        </a>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

