<?php 
$menuActive = 'overview';
$title = 'Project users - Overview';
$pageTitle = 'Project users';
$pageDescription = 'Manage your users';
$headerNavbuttons = [
    __('back_to_projects_list') => [
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
    <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add new user</h3>
            </div>
            <div class="card-body">
    

                <h1><?php echo $user['id'] ? 'Edit' : 'Create'; ?> User</h1>

                <form method="POST" action="/index.php?controller=User&action=<?php echo $user['id'] ? 'update' : 'store'; ?>">
                <?php if ($user['id']): ?>
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        
                        <input type="text" name="password" class="form-control" <?php echo $user['id'] ? '' : 'required'; ?>>
                        <button type="button" class="btn btn-secondary" id="generatePassword">Generate</button>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="isAdmin"
                        <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="isAdmin">Administrator</label>
                </div>



                
                <button type="submit" class="btn btn-primary">Save User</button>
                <a href="/index.php?controller=User&action=index" class="btn btn-secondary">Cancel</a>
            </form>
                </div>
    </div>
</div>
    <!--end::Post-->
</div>
<!--end::Container-->


<script>
    document.getElementById('generatePassword').addEventListener('click', function() {
        const passwordField = document.querySelector('input[name="password"]');
        const generatedPassword = Math.random().toString(36).slice(-12);
        passwordField.value = generatedPassword;
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>