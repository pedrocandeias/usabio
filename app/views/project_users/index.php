<?php 
$menuActive = 'moderators';
$title = 'Moderators Management';
$pageTitle = 'Moderators Management';
$pageDescription = 'Manage the moderators for your projects.';
$headerNavbuttons = [
    __('back_to_projects') => [
        'url' => '/index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_home_primary_button',
    ],
];

require __DIR__ . '/../layouts/header.php'; 
?>


<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Moderators for <?php echo htmlspecialchars($project['title']); ?></h3>
                <div class="card-toolbar">
                    <a href="/index.php?controller=Moderator&action=create&project_id=<?php echo $project['id']; ?>" class="btn btn-primary">Add Moderator</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Assigned Users + Procedure -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php echo $projectAssignedUsers; ?>
                                <?php echo $project['id']; ?>
                                <?php print_r($assignedModeratorIds); ?>
                                <?php print_r($allModerators); ?>
                               
                                <h5 class="card-title">Assigned moderators</h5>
                                <?php if (!empty($assignedUsers)) : ?>
                                    <ul class="list-group mb-4">
                                        <?php foreach ($assignedUsers as $user): ?>
                                            <li class="list-group-item">
                                            <?php if ($user['id'] == $project['owner_id']): ?>
            <span class="badge bg-primary ms-2">Admin</span>
        <?php endif; ?>    
                                            
                                            <?php echo $user['fullname'];?> - <?php echo htmlspecialchars($user['username']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">No moderators assigned to this project.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>