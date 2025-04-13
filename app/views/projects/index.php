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
                <?php foreach ($projects as $project): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                
                                <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                                <p class="text-muted mb-0"><small>Created on: <?php echo date('F j, Y', strtotime($project['created_at'])); ?></small></p>
                                <p class="text-muted mb-0"><small>Last updated: <?php echo date('F j, Y', strtotime($project['updated_at'])); ?></small></p>
                            </div>
                            <div class="card-footer d-flex flex-column gap-2">
                            <a href="/index.php?controller=Session&action=dashboard&project_id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">Go to Test Sessions</a>
                                 <a href="/index.php?controller=Project&action=show&id=<?php echo $project['id']; ?>" class="btn btn-secondary btn-sm w-100">View Project</a>
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
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
