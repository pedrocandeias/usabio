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

        <!--begin::Analytics navigation-->
        <div class="card">
            <div class="card-body">
                <ul class="nav mx-auto flex-shrink-0 flex-center flex-wrap border-transparent fs-6 fw-bold">
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'overview') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=index&id=<?php echo $project['id']; ?>">📊 Overview</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'tasks') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=tasks&id=<?php echo $project['id']; ?>">📋 Task Success</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'questionnaires') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=questionnaires&id=<?php echo $project['id']; ?>">📑 Questionnaires</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'sus') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=sus&id=<?php echo $project['id']; ?>">🧠 SUS</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'participants') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=participants&id=<?php echo $project['id']; ?>">👥 Participants</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--end::Analytics navigation-->


        <!--begin::Card-->
        <div class="card my-5">
            <div class="card-header">
                <h3 class="card-title">Task Performance Overview</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($taskStats)) : ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr class="text-center">
                                <th>Task</th>
                                <th>Success %</th>
                                <th>Fail %</th>
                                <th>Skipped %</th>
                                <th>Median Time (s)</th>
                                <th>Stdev Time</th>
                                <th>Total Responses</th>
                            </tr>
                        </thead>
                        <tbody>
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
                <?php else : ?>
                    <div class="alert alert-info text-center py-4">No task responses found for this project.</div>
                <?php endif; ?>
            </div>
        </div>
        <!--end::Card-->
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
