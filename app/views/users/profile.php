<?php 
$menuActive = 'profile';
$title = 'My Profile';
$pageTitle = 'My Profile';
$pageDescription = 'Manage your profile information';
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">My profile
            </div>
            <div class="card-body">
                <?php if (!empty($_GET['success'])): ?>
                    <div class="alert alert-success">Profile updated successfully.</div>
                <?php endif; ?>

                <form method="POST" action="/index.php?controller=User&action=updateProfile">
                    <div class="mb-3">
                        <label class="form-label">Email (Username)</label>
                        <input type="email" name="email" class="form-control disabled bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
                    </div>

                    <hr class="my-4">

                    <h5>Change Password</h5>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control">
                        <div class="form-text">Leave empty to keep current password</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
