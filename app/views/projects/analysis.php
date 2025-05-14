<?php 
$menuActive = 'analysis';
$title = 'Project Analysis';
$pageTitle = 'Project Analysis';
$pageDescription = 'Manage your project and test sessions.';
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
<div class="content flex-row-fluid" id="kt_content">
<?php require_once __DIR__ . '/../layouts/project-header.php'; ?>   


    <div class="row align-items-center mb-4">
        <?php if (!empty($susBreakdown)) : ?>
        <div class="col-md-4 offset-md-8 text-end">
            <a href="/index.php?controller=Response&action=exportSusCsv&project_id=<?php echo $project['id']; ?>"
                class="btn btn-outline-primary btn-sm">
                📤 Export SUS Data (CSV)
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Sessions</h5>
                    <p class="display-6"><?php echo $totalEvaluations; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Responses</h5>
                    <p class="display-6"><?php echo $totalResponses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Avg. Task Time</h5>
                    <p class="display-6"><?php echo $avgTime; ?> sec</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Question Type Chart -->
    <?php if (!empty($questionTypes)): ?>
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Types Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="questionTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Task Performance -->
    <?php if (!empty($taskStats)) : ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h5 class="mb-0 card-title">🧪 Task Performance Overview</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Task</th>
                        <th># Responses</th>
                        <th>Avg. Time (sec)</th>
                        <th># Errors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taskStats as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task_text']); ?></td>
                            <td><?php echo $task['total_responses']; ?></td>
                            <td><?php echo round($task['avg_time']); ?></td>
                            <td><?php echo $task['error_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Task Charts -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 card-title"><?php echo __('average_time_per_task'); ?></h5>
                </div>
                <div class="card-body">
                    <canvas id="taskTimeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0  card-title">Error Count per Task</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskErrorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h5 class="mb-0 card-title">Success Rate per Task</h5>
        </div>
        <div class="card-body">
            <canvas id="taskSuccessChart"></canvas>
        </div>
    </div>
    <?php else: ?>
        <p class="text-muted">No task responses found for this project.</p>
    <?php endif; ?>
    <?php if (!empty($susBreakdown)) : ?>
<div class="card shadow-sm mb-5">
    <div class="card-header">
        <h5 class="mb-0  card-title">🎯 SUS Score Distribution</h5>
    </div>
    <div class="card-body">
        <canvas id="susScoreChart"></canvas>
    </div>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header">
        <h5 class="mb-0 card-title">Participant Breakdown</h5>
    </div>
    <div class="card-body table-responsive">
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

<?php if (!empty($susSummary)) : ?>
<div class="card shadow-sm mb-5">
    <div class="card-header">
        <h5 class="mb-0 card-title">🧠 SUS Interpretation</h5>
    </div>
    <div class="card-body">
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
</div>
<?php endif; ?>
<?php else: ?>
<p class="text-muted"><?php echo __('no_valid_sus_questionnaires_detected_for_this_project');?>.</p>
<?php endif; ?>
</div> <!-- /.content -->
</div> <!-- /.kt_content_container -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($taskStats)) : ?>
const taskLabels = <?php echo json_encode(array_column($taskStats, 'task_text')); ?>;
const taskAvgTime = <?php echo json_encode(array_map(fn($t) => round($t['avg_time']), $taskStats)); ?>;
const taskErrors = <?php echo json_encode(array_map(fn($t) => (int) $t['error_count'], $taskStats)); ?>;
const taskSuccessRate = <?php echo json_encode(array_column($taskStats, 'success_rate')); ?>;

new Chart(document.getElementById('taskTimeChart'), {
    type: 'bar',
    data: {
        labels: taskLabels,
        datasets: [{
            label: 'Avg Time (s)',
            data: taskAvgTime,
            backgroundColor: '#20c997'
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false }},
        scales: { x: { beginAtZero: true }}
    }
});

new Chart(document.getElementById('taskErrorChart'), {
    type: 'bar',
    data: {
        labels: taskLabels,
        datasets: [{
            label: 'Errors',
            data: taskErrors,
            backgroundColor: '#dc3545'
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false }},
        scales: { x: { beginAtZero: true }}
    }
});

new Chart(document.getElementById('taskSuccessChart'), {
    type: 'bar',
    data: {
        labels: taskLabels,
        datasets: [{
            label: 'Success Rate (%)',
            data: taskSuccessRate,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => `Success Rate: ${ctx.raw}%`
                }
            }
        },
        scales: { x: { beginAtZero: true, max: 100 }}
    }
});
<?php endif; ?>

<?php if (!empty($questionTypes)): ?>
const questionTypeCtx = document.getElementById('questionTypeChart')?.getContext('2d');
if (questionTypeCtx) {
    new Chart(questionTypeCtx, {
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
}
<?php endif; ?>

<?php if (!empty($susBreakdown)): ?>
const susCtx = document.getElementById('susScoreChart')?.getContext('2d');
if (susCtx) {
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
}
<?php endif; ?>
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
