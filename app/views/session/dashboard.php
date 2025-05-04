<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
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
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
    
            <?php if (isset($_GET['success'])) : ?>
                <div class="alert alert-success">✅ Task session saved successfully!</div>
            <?php endif; ?>

            <?php if (!empty($tests)) : ?>
                <?php $projectName = $tests[0]['project_name'] ?? 'Project'; ?>
                <h1 class="mb-4 mx-4">Test Sessions for <?php echo htmlspecialchars($projectName); ?></h1>
        
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($tests as $test): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($test['title']); ?></h5>
                                
                                    <p class="mb-1">
                                        🧩 Task Groups: <?php echo $test['task_group_count']; ?><br>
                                        📋 Questionnaires: <?php echo $test['questionnaire_group_count']; ?>
                                    </p>
                                </div>
                                <div class="card-footer d-flex flex-column gap-2">
                                    <a href="/index.php?controller=Session&action=startTaskSession&test_id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm w-100">Start Task Session</a>
                                    <a href="/index.php?controller=Session&action=startQuestionnaire&test_id=<?php echo $test['id']; ?>" class="btn btn-secondary btn-sm w-100">Start Questionnaire</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    ⚠️ No tests available. Make sure you're assigned to a project with tests.
                </div>
            <?php endif; ?>
        </div>

            </div>
            </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
