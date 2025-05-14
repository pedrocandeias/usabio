<?php 
$menuActive = 'adminsettings';
$title = 'Admin Settings';
$pageTitle = 'Admin Settings';
$pageDescription = 'Manage settings.';
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




<?php require_once __DIR__ . '/../layouts/admin-header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Admin Settings</h3>
    </div>
    <div class="card-body">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✅ Settings updated!</div>
        <?php endif; ?>

        <?php
        if (!empty($_GET['email_success']) && $_GET['email_success'] === 'email_test_sent') { ?>
       <div class="alert alert-success">Email sent successfully</div>
       <?php }
        if (!empty($_GET['email_error']) && $_GET['email_error'] === 'email_test_failed') { ?>
        <div class="alert alert-danger">Failed to send test email</div>
       <?php  } ?>
       


        <form method="POST" action="/index.php?controller=Settings&action=save">
            <div class="mb-3">
                <label class="form-label">Platform Base URL</label>
                <input type="text" class="form-control" name="platform_base_url" value="<?php echo htmlspecialchars($settings['platform_base_url'] ?? ''); ?>">
            </div>
        
            <div class="mb-3">
                <label class="form-label">OpenAI API Key</label>
                <input type="text" class="form-control" name="openai_api_key" value="<?php echo $settings['openai_api_key']; ?>">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enable_ai_features" <?php echo $settings['enable_ai_features'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Enable AI Features</label>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enable_user_registration" <?php echo $settings['enable_user_registration'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Enable User Registration</label>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enable_login" <?php echo $settings['enable_login'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Enable Login</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Default Language</label>
                <select name="default_language" class="form-select">
                    <option value="en" <?php echo $settings['default_language'] === 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="pt" <?php echo $settings['default_language'] === 'pt' ? 'selected' : ''; ?>>Português</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">UI Theme</label>
                <input type="text" class="form-control" name="ui_theme" value="<?php echo htmlspecialchars($settings['ui_theme'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Feature Flags (JSON)</label>
                <textarea class="form-control" name="feature_flags" rows="3"><?php echo htmlspecialchars($settings['feature_flags'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <h3>Mailserver Settings</h3>
                <p>Provide your mailserver configuration below.</p>
            </div>
            <div class="mb-3">
                <label class="form-label">Mailserver Host</label>
                <input type="text" class="form-control" name="mailserver_host" value="<?php echo htmlspecialchars($settings['mailserver_host'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mailserver Port</label>
                <input type="text" class="form-control" name="mailserver_port" value="<?php echo htmlspecialchars($settings['mailserver_port'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mailserver Username</label>
                <input type="text" class="form-control" name="mailserver_username" value="<?php echo htmlspecialchars($settings['mailserver_username'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mailserver Password</label>
                <input type="password" class="form-control" name="mailserver_password" value="<?php echo htmlspecialchars($settings['mailserver_password'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mailserver Encryption</label>
                <select name="mailserver_encryption" class="form-select">
                    <option value="" <?php echo empty($settings['mailserver_encryption']) ? 'selected' : ''; ?>>None</option>
                    <option value="ssl" <?php echo (isset($settings['mailserver_encryption']) && $settings['mailserver_encryption'] === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                    <option value="tls" <?php echo (isset($settings['mailserver_encryption']) && $settings['mailserver_encryption'] === 'tls') ? 'selected' : ''; ?>>TLS</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Support Email</label>
                <input type="email" class="form-control" name="support_email" value="<?php echo htmlspecialchars($settings['support_email']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">No Reply Email</label>
                <input type="email" class="form-control" name="noreplymail" value="<?php echo htmlspecialchars($settings['noreplymail']); ?>">
            </div>
         
            <div class="mb-3">
                <label class="form-label">Send test email to</label>
                <input type="email" class="form-control" name="test_email" value="<?php echo htmlspecialchars($settings['test_email']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
    

    <div class="m-5">
        <!-- Test Email Form: fora do form principal -->
        <form method="POST" action="/index.php?controller=Settings&action=testEmail" class="d-inline mt-4">
            <input type="hidden" name="test_email" value="<?php echo $settings['test_email']; ?>">
            <button type="submit" class="btn btn-info">Test Email</button>
        </form>
    </div>

</div>

    </div>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>