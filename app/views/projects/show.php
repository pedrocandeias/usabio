
<?php 
$title = 'Project details';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
<a href="/index.php?controller=Project&action=index" class="btn btn-secondary btn-xs mb-4">
                    ← Back to Project List
                </a>
    <!-- Header + Edit Button -->
    <div class="mb-4 mt-4">
        <div class="row">
            <div class="col-md-12">
                <a href="/index.php?controller=Project&action=edit&id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                    Edit Project
                </a>
                <a href="/index.php?controller=Project&action=edit&id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                    Delete Project
                </a>
            </div> 
        </div>
    </div>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div class="row w-100">
            <div class="col-md-6">
                <h1 class="display-4"><?php echo htmlspecialchars($project['title']); ?>
                </h1>
             
            </div>
            <div class="col-md-6 text-end">
                 <a href="index.php?controller=Session&action=dashboard&project_id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                    Start testing
                </a>
                <a href="/index.php?controller=Project&action=analysis&id=<?php echo $project['id']; ?>" class="btn btn-outline-primary btn-sm">
                    📊 View Full Project Analysis
                </a>
                <a href="/index.php?controller=Export&action=index&project_id=1=<?php echo $project['id']; ?>" class="btn btn-outline-primary btn-sm">
                    📊 Export data
                </a>
            </div>
        </div>
    </div>



    <!-- First Grid Section -->
    <div class="row">
    <div class="col-md-6">
            <div class="card mb-4 p-1">
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">Created: <strong><?php echo htmlspecialchars($project['created_at']); ?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">Updated: <strong><?php echo htmlspecialchars($project['updated_at']); ?></strong></p>
                </div>
            </div>
        </div>
        
    </div>
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
                    <h5 class="card-title">Assigned moderators</h5>
                    <?php if (!empty($assignedUsers)) : ?>
                        <ul class="list-group mb-4">
                            <?php foreach ($assignedUsers as $user): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($user['username']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No moderators assigned to this project.</p>
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
                        <a href="/index.php?controller=Test&action=show&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-outline-secondary">Manage tasks and questions</a>
                        <a href="/index.php?controller=Test&action=edit&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/index.php?controller=Test&action=destroy&id=<?php echo $test['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this test?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
        ⚠️ No tests found for this project.
        </div>
    <?php endif; ?>

    <!-- Participants list List -->

    <hr class="my-5">
    <div id="participant-list">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Participants</h3>
            <div>
                <a href="/index.php?controller=Participant&action=create&project_id=<?php echo $project['id']; ?>" class="btn btn-success btn-sm">
                    Add Participant
                </a>
            </div>
        </div>
        <?php if (!empty($participants)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Assigned tests</th>
                        <th>Complete tests</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant['participant_name']); ?></td>
                            <td><?php
                                    $tests = $testsByParticipant[$participant['id']] ?? [];
                                    if (!empty($tests)) { ?>
                                        <?php foreach ($tests as $title): ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($title) ?></span>
                    <?php endforeach; ?>

                                    <?php } else {
                                        echo '<span class="text-muted">No tests assigned</span>';
                                    }
                                ?></td>
                                <td>  <?php
                            $completed = $completedTestsByParticipant[$participant['id']] ?? [];
                            if (!empty($completed)) {
                                foreach ($completed as $title) {
                                    echo '<span class="badge bg-success me-1">' . htmlspecialchars($title) . '</span>';
                                }
                            } else {
                                echo '<span class="text-muted">None</span>';
                            }
                        ?>
                            </td>
                            <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="/index.php?controller=Participant&action=show&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>">View</a>
                       
                       
                       <a href="/index.php?controller=Participant&action=edit&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                       <a href="/index.php?controller=Participant&action=destroy&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>"
                          class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('Are you sure you want to delete this participant?');">Delete</a>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                ⚠️ No participants found for this project.
            </div>
        <?php endif; ?>
    </div>


     <!-- Custom Participant Fields -->
     <div id="custom-fields-list">
        <hr class="my-5">
        <h4 class="mb-3">Custom Participant Fields</h4>
       
        <form method="POST" action="/index.php?controller=CustomField&action=store" class="row g-3 mb-4">
            <input type="hidden" name="project_id" value=" <?php echo $project['id']; ?>">

            <div class="col-md-4">
                <input type="text" name="label" class="form-control" placeholder="Field Label" required>
            </div>

            <div class="col-md-4">
                <select name="field_type" class="form-select" required>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Dropdown (select)</option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="options" class="form-control" placeholder="Options (for select, e.g. A;B;C)">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-success w-100">Add</button>
            </div>
        </form>

        <?php if (!empty($customFields)) : ?>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Options</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customFields as $field): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($field['label']); ?></td>
                            <td><?php echo $field['field_type']; ?></td>
                            <td><?php echo htmlspecialchars($field['options']); ?></td>
                            <td class="text-end">
                            <a href="/index.php?controller=ParticipantCustomField&action=edit&id=<?php echo $field['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
<a href="/index.php?controller=ParticipantCustomField&action=destroy&id=<?php echo $field['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this field?')">Delete</a>


                      
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                ⚠️ No custom fields for participants found for this project.
            </div>
        <?php endif; ?>
    </div>
   
    <div class="mt-4">
        <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">← Back to Project List</a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

