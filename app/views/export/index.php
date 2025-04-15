<?php
$title = 'Export Data';
require __DIR__ . '/../layouts/header.php';

?>


<div class="container py-5">
    <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>

    <h1 class="mb-4">Export Data: <?= htmlspecialchars($project['title']) ?></h1>


    <div class="row">
        <div class="col-md-12">
            <div class="mb-4">
                <h5>📋 Participant Data</h5>
                <a href="/index.php?controller=Export&action=demographicsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                    Download Demographics CSV
                </a>
                <a href="/index.php?controller=Export&action=fullCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                    Download Demographics + All Responses CSV
                </a>
            </div>
            <div class="mb-4">
                <h5>📋 Evaluation Data</h5>
                <a href="/index.php?controller=Export&action=exportTaskEvaluationsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary  w-100 mb-2">
                    Download Task Evaluations (with Demographics)
                </a>
                <a href="/index.php?controller=Export&action=exportQuestionnaireEvaluationsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-success w-100 mb-2">
                    Download Questionnaire Evaluations (with Demographics)
                </a>
            </div>



            <div class="mb-4">
    <h5>📁 Per-Test Exports</h5>
    <?php foreach ($tests as $t): ?>
        <div class="mb-2">
            <h6><?php echo htmlspecialchars($t['title']) ?></h6>
            <span>
                <a href="/index.php?controller=Export&action=exportTaskResponsesByTest&test_id=<?= $t['id'] ?>" class="btn btn-outline-success w-100 mb-2">Tasks</a>
                <a href="/index.php?controller=Export&action=exportQuestionnaireResponsesByTest&test_id=<?= $t['id'] ?>" class="btn btn-outline-success w-100 mb-2">Questionnaire</a>
            </span>
        </div>
    <?php endforeach; ?>
</div>


            <div class="mb-4">
                <h5>📊 SUS Results</h5>
                <a href="/index.php?controller=Export&action=susCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-secondary w-100 mb-2">
    Export SUS Scores (System Usability Scale)
</a>
            </div>



        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
