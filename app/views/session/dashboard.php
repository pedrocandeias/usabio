<!-- app/views/projects/index.php -->
<?php 
$title = 'Projects';
require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>
    
    
    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success">✅ Task session saved successfully!</div>
    <?php endif; ?>

    <?php if (!empty($tests)) : ?>
        <?php $projectName = $tests[0]['project_name'] ?? 'Project'; ?>
        <h1 class="mb-4">Test Sessions for <?php echo htmlspecialchars($projectName); ?></h1>
   
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
                            <a href="/index.php?controller=Session&action=startTaskSession&test_id=<?php echo $test['id']; ?>" class="btn btn-outline-primary btn-sm w-100">Start Task Session</a>
                            <a href="/index.php?controller=Session&action=startQuestionnaire&test_id=<?php echo $test['id']; ?>" class="btn btn-outline-secondary btn-sm w-100">Start Questionnaire</a>
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


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
