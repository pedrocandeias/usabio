<?php 
$projectBase = $project;
$menuActive = 'analysis';
$title = 'Task Analysis';
$pageTitle = 'Task-level Analysis';
$pageDescription = 'Detailed breakdown of participant performance on tasks.';
$headerNavbuttons = [
    'Back to project' => [
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

        <h3 class="fw-bold my-4">Task Analysis for <?php echo htmlspecialchars($project['title']); ?></h3>

        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Attempts</th>
                    <th>Success %</th>
                    <th>Error %</th>
                    <th>Skipped %</th>
                    <th>Median Time (s)</th>
                    <th>Stdev Time</th>
                </tr>
            </thead>
            <tbody>
                <?php print_r($taskStats); ?>
                <?php if (!empty($taskStats)): ?>
                    <?php foreach ($taskStats as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task_text']); ?></td>
                            <td><?php echo $task['total_attempts']; ?></td>
                            <td><?php echo $task['success_rate']; ?>%</td>
                            <td><?php echo $task['error_rate']; ?>%</td>
                            <td><?php echo $task['skipped_percent']; ?>%</td>
                            <td><?php echo $task['median_time']; ?></td>
                            <td><?php echo $task['stdev_time']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No task data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
