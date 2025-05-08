<?php 
$menuActive = 'invites';
$title = 'My Profile - Invitations';
$pageTitle = 'My Profile - Invitations';
$pageDescription = 'Manage your invitation to projects';
$headerNavbuttons = [
    'Back to Projects' => [
        'url' => 'index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_dashboard_button',
    ],
];

require __DIR__ . '/../layouts/header.php'; 
?>



<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">


    <div class="card mb-5">
        <div class="card-body py-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 active" href="index.php?controller=User&action=profile">
                        <?php echo __('account_settings'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 " href="index.php?controller=Invite&action=index">
                        <?php echo __('project_invitations'); ?>
                    </a>
                </li>   
            </ul>
        </div>
    </div>
    
        <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php if ($_GET['success'] === 'accepted') echo "You have accepted the invitation."; ?>
            <?php if ($_GET['success'] === 'declined') echo "You have declined the invitation."; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Project Invitations</h3>
            </div>
            <div class="card-body">

            

        
            <?php if (empty($invites)): ?>
                <div class="alert alert-info"><?php echo __('you_have_no_pending_invitations_at_the_moment'); ?>.</div>
            <?php else: ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?php echo __('project'); ?></th>
                            <th><?php echo __('invited_on'); ?></th>
                            <th><?php echo __('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invites as $invite): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invite['project_title']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($invite['created_at'])); ?></td>
                                <td>
                                    <form action="index.php?controller=Invite&action=respond" method="post" class="d-inline">
                                        <input type="hidden" name="invite_id" value="<?php echo $invite['id']; ?>">
                                        <input type="hidden" name="action" value="accepted">
                                        <button class="btn btn-success btn-sm"><?php echo __('accept'); ?></button>
                                    </form>
                                    <form action="index.php?controller=Invite&action=respond" method="post" class="d-inline ms-2">
                                        <input type="hidden" name="invite_id" value="<?php echo $invite['id']; ?>">
                                        <input type="hidden" name="action" value="declined">
                                        <button class="btn btn-outline-secondary btn-sm"><?php echo __('decline'); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <a href="index.php?controller=Project&action=index" class="btn btn-link mt-4">&larr; <?php echo __('back_to_moderator_management'); ?></a>

        </div>
        </div>
    </div>
</div>


<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>