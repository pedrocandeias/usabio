<?php 
$menuActive = 'adminsettings';
$title = 'Admin Settings';
$pageTitle = 'Admin Settings';
$pageDescription = 'Manage settings.';
$headerNavbuttons = [
    'Back to projects list' => [
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

        <form method="POST" action="/index.php?controller=Settings&action=save">
            <div class="mb-3">
                <label class="form-label">OpenAI API Key</label>
                <input type="text" class="form-control" name="openai_api_key" value="<?php echo htmlspecialchars($decrypted); ?>">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enable_apikey" <?php echo $settings['enable_apikey'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Enable AI Features</label>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enable_registration" <?php echo $settings['enable_registration'] ? 'checked' : ''; ?>>
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

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="allow_registration" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                <label class="form-check-label">Allow Public Registration</label>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

    </div>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>