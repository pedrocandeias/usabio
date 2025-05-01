<?php
$menuActive = 'admin';
$title = 'Generate Fake Data';
$pageTitle = 'Generate Fake Test Data';
$pageDescription = 'Generate synthetic evaluations, task responses and questionnaire answers for testing purposes.';
$headerNavbuttons = [
    'Back to dashboard' => [
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
            <h3 class="card-title">🧪 Fake Data Generator</h3>
            </div>
            <div class="card-body">
            <form method="GET" action="/index.php">
    <input type="hidden" name="controller" value="DataSeeder">
    <input type="hidden" name="action" value="index">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Select Project</label>
                        <select name="select_project_id" class="form-select" onchange="this.form.submit()">
                            <option value="">— Choose —</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $selectedProjectId == $p['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['title']); ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (!empty($tests)): ?>
                <form method="POST" action="/index.php?controller=DataSeeder&action=generate">
                    <input type="hidden" name="select_project_id" value="<?php echo $selectedProjectId; ?>">
                    
                    <div class="mb-4">
                        <label class="form-label">Select Test</label>
                        <select name="test_id" class="form-select" required>
                        <option value="">— Choose a test —</option>
                        <?php foreach ($tests as $test): ?>
                            <option value="<?php echo $test['id']; ?>"><?php echo htmlspecialchars($test['title']); ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Number of Evaluations to Generate</label>
                        <input type="number" name="count" class="form-control" min="1" max="100" value="10" required>
                    </div>

                    <button type="submit" class="btn btn-success">Generate Fake Results</button>
                </form>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
