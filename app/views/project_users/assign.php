<?php 

$menuActive = 'moderators';
$title = 'Moderators Management';
$pageTitle = 'Moderators Management';
$pageDescription = 'Manage the moderators for your project.';
$headerNavbuttons = [
    'Back to project' => [
        'url' => '/index.php?controller=Project&action=show&id=' . $project_id ,
        'icon' => 'ki-duotone ki-black-left fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_moderator">Assign Moderators</button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($assignedUsers)) : ?>
                    <ul class="list-group">
                        <?php foreach ($assignedUsers as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                                <a href="/index.php?controller=ProjectUser&action=delete&project_id=<?php echo $project_id; ?>&user_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No moderators assigned to this project.</p>
                <?php endif; ?>
            </div>
        </div>

        <!--begin::Modal - Assign Moderators -->
        <div class="modal fade" id="kt_modal_add_moderator" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Assign Moderators</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-10">
                        <form method="POST" action="/index.php?controller=ProjectUser&action=save">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <div class="mb-4">
                                <label class="form-label">Select moderators to assign:</label>
                                <select name="assigned_users[]" class="form-select" multiple size="10">
                                    <?php foreach ($allModerators as $moderator): ?>
                                        <option value="<?php echo $moderator['id']; ?>" <?php echo in_array($moderator['id'], $assignedModeratorIds) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($moderator['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple moderators.</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Modal-->
    </div>
</div>
<!--end::Container-->

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
