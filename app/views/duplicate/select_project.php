<?php 
$menuActive = 'Start tasks';
$title = 'Project tasks - Overview';
$pageTitle = 'Project Tasks - Overview';
$pageDescription = 'Start Task session.';
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

        

                    <div class="container py-5">
                        <h1>Duplicate a Project</h1>
                        <form method="GET" action="/index.php">
                            <input type="hidden" name="controller" value="Duplicate">
                            <input type="hidden" name="action" value="duplicateProject">
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Select Project</label>
                                <select name="id" id="project_id" class="form-select" required>
                                    <?php foreach ($projects as $p): ?>
                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Duplicate Selected Project</button>
                        </form>
                    </div>

                </div>
             </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

