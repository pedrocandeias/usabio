<?php 
$projectBase = $project;
$menuActive = 'analysis';
$activeTab = 'participants';
$title = 'Participants Analysis';
$pageTitle = 'Participants Analysis';
$pageDescription = 'Review participant activity and demographics.';
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

        <div class="card my-5">
            <div class="card-header">
                <h3 class="card-title">Participants for <?php echo htmlspecialchars($project['title']); ?></h3>
            </div>
            <div class="card-body">
                <?php if (!empty($participants)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                           <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Academic Level</th>
                                    <th>Tasks Completed</th>
                                    <th>Task Success %</th>
                                    <th>Questionnaire Completed</th>
                                    <th>SUS Score</th>
                                    <th>Custom Fields</th> <!-- NEW -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['participant_name'] ?? '') ?: 'Anonymous'; ?></td>
                                        <td><?php echo htmlspecialchars($p['participant_age'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($p['participant_gender'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($p['participant_academic_level'] ?? ''); ?></td>
                                        <td><?php echo $p['tasks_completed'] ?? 0; ?></td>
                                        <td><?php echo $p['task_success'] ?? 0; ?>%</td>
                                        <td>
                                            <?php if ($p['questionnaire_completed']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $p['sus_score'] !== null ? round($p['sus_score'], 1) : '-'; ?>
                                        </td>
                                       <td>
                                            <?php if (!empty($p['custom_fields'])): ?>
                                                <?php foreach ($p['custom_fields'] as $fieldLabel => $fieldValue): ?>
                                                    <span class="badge bg-light text-dark border border-secondary fw-normal me-1 mb-1">
                                                        <?php echo htmlspecialchars($fieldLabel); ?>: <?php echo htmlspecialchars($fieldValue ?? '-'); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                                            
                                            </div>
                                            <?php if (!empty($correlationData)): ?>

        <div class="card-header">
            <h3 class="card-title">Demographics Correlation (Task Success vs SUS Score)</h3>
        </div>
        <div class="card-body">
            <?php foreach ($correlationData as $groupType => $groups): ?>
                <?php 
$groupLabel = str_starts_with($groupType, 'custom_field_') ? 
              'Custom Field: ' . substr($groupType, 13) : 
              ucfirst(str_replace('_', ' ', $groupType));
?>
<h4 class="fw-bold mt-4 text-capitalize"><?php echo $groupLabel; ?></h4>
                <?php if (!empty($groups)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo ucfirst($groupType); ?></th>
                                    <th>Participants</th>
                                    <th>Avg Task Success %</th>
                                    <th>Avg SUS Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groups as $value => $stats): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($value); ?></td>
                                        <td><?php echo $stats['participants'] ?? 0; ?></td>
                                        <td><?php echo $stats['avg_task_success'] ?? '-'; ?>%</td>
                                        <td><?php echo $stats['avg_sus'] !== null ? $stats['avg_sus'] : '-'; ?></td>


                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No data available for <?php echo $groupType; ?>.</p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>                
                </div>

    
                                            </div>
<?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">No participants found for this project.</div>
                <?php endif; ?>
       
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
