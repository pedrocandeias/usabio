<?php 
$projectBase = $project;
$menuActive = 'analysis';
$activeTab = 'questionnaires';
$title = 'Questionnaire Analysis';
$pageTitle = 'Questionnaire Analysis';
$pageDescription = 'Review participant answers to questionnaire items.';
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
                <h3 class="card-title">Questionnaire Analysis</h3>
            </div>
        </div>


        <?php 
        
        if (!empty($questionStats)): ?>
            <?php foreach ($questionStats as $q): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo htmlspecialchars($q['text']); ?></h4>
                        <?php if ($q['inconsistent']): ?>
                            <span class="badge bg-warning text-dark">High inconsistency (variance: <?php echo $q['variance']; ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php 
                        if (!empty($q['counts'])): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Option</th>
                                        <th>Responses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($q['counts'] as $value => $count): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($q['options'][$value] ?? $value); ?></td>
                                            <td><?php echo $count; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No answers recorded for this question.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">No questionnaire data found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
