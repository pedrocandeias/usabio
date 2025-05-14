<?php 
$menuActive = 'emailtemplates';
$title = 'Email Templates';
$pageTitle = 'Email Templates';
$pageDescription = 'Manage the content of platform emails.';
$headerNavbuttons = [
    'Back to Admin' => [
        'url' => '/index.php?controller=Settings&action=index',
        'icon' => 'ki-duotone ki-settings fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
    ],
];

require __DIR__ . '/../layouts/header.php'; 
?>

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/admin-header.php'; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Email Templates</h3>
    
            </div>
            <div class="card-body">
                <?php if (!empty($_GET['success'])): ?>
                    <div class="alert alert-success">Template updated successfully.</div>
                <?php endif; ?>
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['toast_success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['toast_success']; ?></div>
                    <?php unset($_SESSION['toast_success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['toast_error'])): ?>
                    <div class="alert alert-danger">❌ <?php echo $_SESSION['toast_error']; ?></div>
                    <?php unset($_SESSION['toast_error']); ?>
                <?php endif; ?>
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Subject</th>
                            <th>Last Updated</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($template['template_key']); ?></code></td>
                                <td><?php echo htmlspecialchars($template['subject']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($template['updated_at'])); ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="/index.php?controller=EmailTemplate&action=edit&id=<?php echo $template['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="/index.php?controller=EmailTemplate&action=testSingle&template=<?php echo urlencode($template['template_key']); ?>" class="btn btn-sm btn-secondary ms-1">Test</a>
                                </td>


                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($templates)): ?>
                            <tr><td colspan="4" class="text-muted text-center">No templates found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
