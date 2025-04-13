<?php 
$title = 'Project details';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">

    <!-- Header + Edit Button -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h1 class="display-4"><?php echo htmlspecialchars($project['title']); ?></h1>
        <a href="/index.php?controller=Project&action=edit&id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">Edit / Remove</a>
    </div>

    <p class="lead"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
    <p>Created on: <strong><?php echo htmlspecialchars($project['created_at']); ?></strong></p>
    <p>Updated on: <strong><?php echo htmlspecialchars($project['updated_at']); ?></strong></p>

    <!-- Analysis Button -->
    <div class="mb-4">
        <a href="/index.php?controller=Project&action=analysis&id=<?php echo $project['id']; ?>" class="btn btn-outline-primary btn-sm">
            📊 View Full Project Analysis
        </a>
    </div>

    <!-- First Grid Section -->
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
    </div>

    <!-- Assigned Users + Procedure -->
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Assigned Users</h5>
                    <?php if (!empty($assignedUsers)) : ?>
                        <ul class="list-group mb-4">
                            <?php foreach ($assignedUsers as $user): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($user['username']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No users assigned to this project.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Procedure</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($project['test_procedure'])); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <a href="/index.php?controller=Participant&action=index&project_id=<?php echo $project['id']; ?>" class="btn btn-outline-dark btn-sm">
    👤 View Participants
</a>


    <!-- Test List -->
    <div id="tests-list" class="d-flex justify-content-between align-items-center mb-3 mt-5">
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
                    <div class="d-flex gap-2">
                        <a href="/index.php?controller=Test&action=show&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
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
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
