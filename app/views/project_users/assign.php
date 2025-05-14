<?php 

$menuActive = 'moderators';
$title = 'Moderators Management';
$pageTitle = 'Moderators Management';
$pageDescription = 'Manage the moderators for your project.';
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
                <h3 class="card-title"><?php echo __('moderators_for'); ?> <?php echo htmlspecialchars($project['title']); ?></h3>
                <div class="card-toolbar">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_moderator">Assign Moderators</button>
                </div>
            </div>
                  
            <div class="card-body">
            <?php if (!empty($visibleModerators)) : ?>
            <ul class="list-group">
            <?php foreach ($visibleModerators as $key => $moderator): ?>
            <?php
                $id = $moderator['id'];
                $isAssigned = in_array($id, $assignedModeratorIds);
                $isPending = in_array($id, $pendingInviteModeratorIds);
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center fs-5">
                <span>
                    <?php if (!empty($moderator['email']) && empty($moderator['fullname'])): ?>
                        <?php echo htmlspecialchars($moderator['email']); ?>
                    <?php else: ?>
                        <?php echo htmlspecialchars($moderator['fullname'] ?? $moderator['username']); ?>
                    <?php endif; ?>

                    <?php if ($moderator['status'] === 'assigned'): ?>
                        <span class="badge bg-success ms-2"><?php echo __('assigned'); ?></span>
                    <?php elseif ($moderator['status'] === 'pending'): ?>
                        <span class="badge bg-warning text-dark ms-2"><?php echo __('pending_invite'); ?></span>
                    <?php elseif ($moderator['status'] === 'email_sent'): ?>
                        <span class="badge bg-info text-white ms-2"><?php echo __('email_sent'); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($moderator['is_admin'])): ?>
                        <span class="badge bg-success ms-2">Admin</span>
                    <?php endif; ?>
                </span>

                <div class="d-flex gap-2">

<?php if ($moderator['status'] === 'pending'): ?>
    <?php if ($this->userIsProjectAdmin($project_id)): ?>
        <a href="/index.php?controller=ProjectUser&action=cancelInvite&project_id=<?php echo $project_id; ?>&invite_id=<?php echo $moderator['invite_id']; ?>"
           class="btn btn-outline-danger btn-sm"
           onclick="return confirm('Are you sure you want to cancel this invite?');">
           Cancel Invite
        </a>
    <?php endif; ?>
<?php endif; ?>
     

                
                <?php if ($moderator['status'] === 'assigned'): ?>
    <?php if ($this->userIsProjectAdmin($project_id) && $moderator['id'] != $project['owner_id']): ?>
        <?php if (!empty($moderator['is_admin'])): ?>
            <form method="POST" action="/index.php?controller=ProjectUser&action=demote" class="d-inline">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <input type="hidden" name="moderator_id" value="<?php echo $moderator['id']; ?>">
                <button class="btn btn-outline-secondary btn-sm">Remove Admin</button>
            </form>
        <?php else: ?>
            <form method="POST" action="/index.php?controller=ProjectUser&action=promote" class="d-inline">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <input type="hidden" name="moderator_id" value="<?php echo $moderator['id']; ?>">
                <button class="btn btn-outline-primary btn-sm">Promote to Admin</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <a href="/index.php?controller=ProjectUser&action=delete&project_id=<?php echo $project_id; ?>&user_id=<?php echo $moderator['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
<?php endif; ?>
                </div>
        </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="text-muted">No moderators found.</p>
<?php endif; ?>

            </div>
        </div>

        <!--begin::Modal - Assign Moderators -->
        <div class="modal fade" id="kt_modal_add_moderator" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><?php echo __('assign_moderators'); ?></h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-10">
                       
                        <form method="POST" action="/index.php?controller=Invite&action=create">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <div class="mb-4">
                                <label class="form-label"><?php echo __('invite_by_email'); ?></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter moderator's email" required />
                                <small class="text-muted"><?php echo __('if_the_user_does_not_exist_they_will_be_invited_to_register'); ?>.</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary"><?php echo __('send_invite'); ?></button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
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


<?php
$toastMessage = '';
$toastType = 'success'; // ou 'danger' se for erro

if (!empty($_GET['success'])) {
    switch ($_GET['success']) {
        case 'moderator_removed':
            $toastMessage = 'Moderator removed successfully.';
            break;
        case 'invite_sent':
            $toastMessage = 'Invite sent successfully.';
            break;
        case 'invite_email_sent':
            $toastMessage = 'Email invitation sent.';
            break;
    }
}

if (!empty($_GET['error'])) {
    $toastMessage = htmlspecialchars($_GET['error']);
    $toastType = 'danger';
}
?>

<?php if (!empty($toastMessage)): ?>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const toastEl = document.getElementById('savedToast');
            const toastMsg = document.getElementById('toastMessage');

            if (toastEl && toastMsg) {
                toastMsg.innerText = "<?php echo $toastMessage; ?>";

                toastEl.classList.remove('text-bg-success', 'text-bg-danger');
                toastEl.classList.add('text-bg-<?php echo $toastType; ?>');

                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
<?php endif; ?>


</body>
</html>
