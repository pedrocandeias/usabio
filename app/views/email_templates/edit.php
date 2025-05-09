<?php 
$menuActive = 'emailtemplates';
$title = 'Edit Email Template';
$pageTitle = 'Edit Email Template';
$pageDescription = 'Edit the subject and body of this email.';
$headerNavbuttons = [
    'Back to Templates' => [
        'url' => '/index.php?controller=EmailTemplate&action=index',
        'icon' => 'ki-duotone ki-arrow-left fs-2',
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
                <h3 class="card-title">Edit Template: <code><?php echo htmlspecialchars($template['template_key']); ?></code></h3>
            </div>
            <div class="card-body">
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="/index.php?controller=EmailTemplate&action=update">
                    <input type="hidden" name="id" value="<?php echo $template['id']; ?>">

                    <div class="mb-4">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required value="<?php echo htmlspecialchars($template['subject']); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Body (HTML allowed)</label>
                        <textarea name="body" class="form-control" rows="10" required><?php echo htmlspecialchars($template['body']); ?></textarea>
                        <small class="text-muted">You can use placeholders like <code>{{project_title}}</code>, <code>{{login_url}}</code>, <code>{{fullname}}</code>, <code>{{link}}</code>.</small>
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
