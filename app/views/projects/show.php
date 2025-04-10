<?php 
$title = 'Project details';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="align-items-center mt-3">
    <a href="/index.php?controller=Project&action=edit&id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">Edit / Remove</a>
    <h1 class="display-4"><?php echo htmlspecialchars($project['title']); ?></h1>
    <p class="lead"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

    </div>
                        
    <div class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Product under test</h5>
                        <p class="card-text"><?php echo htmlspecialchars($project['product_under_test']); ?></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Business Case</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['business_case'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex flex-column">
                <div class="card mb-4 flex-grow-1">
                    <div class="card-body">
                        <h5 class="card-title">Test Objectives</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['test_objectives'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Participants</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['participants'])); ?></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Equipment</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['equipment'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Responsibilities</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['responsibilities'])); ?></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Location & Dates</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['location_dates'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="cold-md-12">
            <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Procedure</h5>
                        <p class="card-text"> <p><?php echo nl2br(htmlspecialchars($project['test_procedure'])); ?></p></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tests</h3>
        <a href="/index.php?controller=Test&action=create&project_id=<?php echo $project['id']; ?>" class="btn btn-success btn-sm">Add Test</a>
    </div>

    <?php if (!empty($tests)) : ?>
        <ul class="list-group">
            <?php foreach ($tests as $test): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($test['title']); ?></strong><br>
                        <small><?php echo htmlspecialchars($test['description']); ?></small>
                    </div>
                    <div>
                        <a href="/index.php?controller=Test&action=edit&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/index.php?controller=Test&action=destroy&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this test?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No tests found for this project.</p>
    <?php endif; ?>

    <div class="mt-4">
        <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">← Back to Project List</a>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>

<!-- Bootstrap 5.3.1 JS Bundle -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
></script>
</body>
</html>
