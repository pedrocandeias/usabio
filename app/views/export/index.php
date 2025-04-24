<?php
$title = 'Export Data';
require __DIR__ . '/../layouts/header.php';

?>


<div class="container py-5">
    <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>

    <h1 class="mb-4">Export Data: <?= htmlspecialchars($project['title']) ?></h1>


    <div class="row" data-masonry='{"percentPosition": true }'>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📋 Participant Data</h5>
                    <a href="/index.php?controller=Export&action=demographicsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                        Download Demographics CSV
                    </a> 
                    <a href="/index.php?controller=Export&action=fullCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                        Download Demographics + All Responses CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📋 Evaluation Data</h5>
                    <a href="/index.php?controller=Export&action=exportTaskEvaluationsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                        Download Task Evaluations (with Demographics)
                    </a>
                    <a href="/index.php?controller=Export&action=exportQuestionnaireEvaluationsCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-success w-100 mb-2">
                        Download Questionnaire Evaluations (with Demographics)
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📁 Per-Test Exports</h5>
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
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📊 SUS Results</h5>
                    <a href="/index.php?controller=Export&action=susCSV&project_id=<?= $project['id'] ?>" class="btn btn-outline-secondary w-100 mb-2">
                        Export SUS Scores (System Usability Scale)
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📦 Full Project Export</h5>
                    <a href="/index.php?controller=Export&action=exportProjectJSON&project_id=<?php echo $project['id']; ?>" class="btn btn-outline-primary w-100 mb-2">
                        ⬇️ Download Project (JSON)
                    </a>
                    <p class="text-muted mt-2">Exports the entire project including tests, task groups, tasks, questionnaire groups, questions, participants, and custom fields.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
