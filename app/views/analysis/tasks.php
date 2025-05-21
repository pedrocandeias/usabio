<?php 
$projectBase = $project;
$title = 'Task Analysis - ' . $project['title'];
$pageTitle = 'Task Analysis';
$pageDescription = 'View success rates, errors, time metrics and skipped tasks.';
$menuActive = 'analysis';
$activeTab = 'tasks';
$headerNavbuttons = [
    'Back to Project' => [
        'url' => '/index.php?controller=Project&action=show&id=' . $project['id'],
        'icon' => 'ki-duotone ki-black-left fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
    ],
];

require __DIR__ . '/../layouts/header.php';
?>

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>

      <?php require_once __DIR__  . '/../layouts/analysis-nav.php'; ?>

        <!--begin::Card-->
        <div class="card my-5">
            <div class="card-header">
                <h3 class="card-title">Task Performance Overview</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($taskStats)) : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr class="fw-bold  bg-primary">
                                    <th class="ps-4 rounded-star text-white"><?php echo __('task'); ?></th>
                                    <th class="text-white">Success %</th>
                                    <th class="text-white">Fail %</th>
                                    <th class="text-white">Skipped %</th>
                                    <th class="text-white">Median Time (s)</th>
                                    <th class="text-white">Stand. Deviation Time</th>
                                    <th class="text-white text-end rounded-end">Total Responses</th>
                                </tr>
                            </thead>
                            <tbody class="fs-5">
                                <?php foreach ($taskStats as $task): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['task_text']); ?></td>
                                        <td class="text-center"><?php echo $task['success_rate']; ?>%</td>
                                        <td class="text-center text-danger"><?php echo $task['fail_rate']; ?>%</td>
                                        <td class="text-center"><?php echo $task['skipped_rate']; ?>%</td>
                                        <td class="text-center"><?php echo $task['median_time']; ?></td>
                                        <td class="text-center"><?php echo $task['stddev_time']; ?></td>
                                        <td class="text-center"><?php echo $task['total_responses']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info text-center py-4"><?php echo __('no_task_responses_found_for_this_project');?>.</div>
                <?php endif; ?>
            </div>
        </div>
        <!--end::Card-->

        <!--begin::Charts-->
<!-- Gráficos -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">Task Success Rates</h3>
            </div>
            <div class="card-body">
                <canvas id="successFailChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">Median Task Time</h3>
            </div>
            <div class="card-body">
                <canvas id="medianTimeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!--end::Charts-->

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

<script>
    const taskChartData = <?php echo json_encode($chartData); ?>;
document.addEventListener('DOMContentLoaded', () => {
    const successFailCtx = document.getElementById('successFailChart').getContext('2d');
    const medianTimeCtx = document.getElementById('medianTimeChart').getContext('2d');

    // Gráfico horizontal Success/Fail/Skipped
    new Chart(successFailCtx, {
        type: 'bar',
        data: {
            labels: taskChartData.labels,
            datasets: [
                {
                    label: 'Success %',
                    data: taskChartData.successRates,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                    label: 'Fail %',
                    data: taskChartData.failRates,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                },
                {
                    label: 'Skipped %',
                    data: taskChartData.skippedRates,
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                },
            ]
        },
        options: {
            responsive: true,
            indexAxis: 'y', // <-- Torna o gráfico horizontal
            scales: {
                x: { beginAtZero: true, max: 100 }
            }
        }
    });

    // Gráfico horizontal Median Time
    new Chart(medianTimeCtx, {
        type: 'bar',
        data: {
            labels: taskChartData.labels,
            datasets: [{
                label: 'Median Time (s)',
                data: taskChartData.medianTimes,
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y', // <-- Torna o gráfico horizontal
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
});
</script>


</body>
</html>
