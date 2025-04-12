<?php 
$title = 'Project Analysis';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h1>📈 Analysis: <?php echo htmlspecialchars($project['product_under_test']); ?></h1>
    </div>
    <?php if (!empty($susBreakdown)) : ?>
        <div class="col-md-4 text-end">
            <a href="/index.php?controller=Response&action=exportSusCsv&project_id=<?php echo $project['id']; ?>"
               class="btn btn-outline-primary btn-sm">
                📤 Export SUS Data (CSV)
            </a>
        </div>
    <?php endif; ?>
</div>


    
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card text-bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Sessions</h5>
                    <p class="display-6"><?php echo $totalEvaluations; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Responses</h5>
                    <p class="display-6"><?php echo $totalResponses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Avg. Task Time</h5>
                    <p class="display-6"><?php echo $avgTime; ?> sec</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <h4 class="mb-3">Question Types Distribution</h4>
            <div class="border rounded p-3 bg-white shadow-sm">
                <canvas id="questionTypeChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <?php if (!empty($susBreakdown)) : ?>
    <hr class="my-5">
    <h3 class="mb-4">🎯 SUS Analysis</h3>

    <div class="row">
        <div class="col-md-5">
            <div class="mb-4">
                <h5>SUS Score Distribution</h5>
                <div class="border rounded p-3 bg-white shadow-sm">
                    <canvas id="susScoreChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="mb-4">
                <h5>Participant Breakdown</h5>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <th>Q<?php echo $i; ?></th>
                            <?php endfor; ?>
                            <th>SUS</th>
                            <th>Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($susBreakdown as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['participant']); ?></td>
                                <?php foreach ($row['answers'] as $ans): ?>
                                    <td><?php echo $ans; ?></td>
                                <?php endforeach; ?>
                                <td><?php echo $row['score']; ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                        echo match ($row['label']) {
                                            'Excellent' => 'success',
                                            'Good' => 'primary',
                                            'OK' => 'warning',
                                            default => 'danger'
                                        };
    ?>"><?php echo $row['label']; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <?php if (!empty($susSummary)) : ?>
        <div class="alert alert-light border rounded mt-4">
            <h5 class="mb-3">🧠 SUS Interpretation</h5>
            <ul class="mb-0">
                <li>
                    The average SUS score is <strong><?php echo $susSummary['average']; ?></strong>, which is considered 
                    <strong><?php echo $susSummary['label']; ?></strong> usability.
                </li>
                <?php if ($susSummary['low'] > 0) : ?>
                    <li>
                        <strong><?php echo $susSummary['low']; ?></strong> participant<?php echo $susSummary['low'] > 1 ? 's' : ''; ?> scored below 50,
                        indicating potential usability concerns.
                    </li>
                <?php endif; ?>
                <li>
                    Overall score variation is <strong><?php echo $susSummary['variation']; ?></strong>, suggesting
                    <?php
                        echo match($susSummary['variation']) {
                            'high' => 'inconsistent experience among users.',
                            'moderate' => 'some variation in perception.',
                            default => 'a consistent perception of usability.'
                        };
    ?>
                </li>
            </ul>
        </div>
        <?php endif; ?>
<?php else: ?>
    <p class="text-muted">No valid SUS questionnaires detected for this project.</p>
<?php endif; ?>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('questionTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($questionTypes, 'question_type')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($questionTypes, 'count')); ?>,
                backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // SUS Score Bar Chart
    const susCtx = document.getElementById('susScoreChart').getContext('2d');
    new Chart(susCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($susBreakdown, 'participant')); ?>,
            datasets: [{
                label: 'SUS Score',
                data: <?php echo json_encode(array_column($susBreakdown, 'score')); ?>,
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `Score: ${ctx.raw}`
                    }
                }
            }
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
