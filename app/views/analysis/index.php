<?php 
$menuActive = 'analysis';
$activeTab = 'overview';
$title = 'Project Analysis';
$pageTitle = 'Project Analysis';
$pageDescription = 'Manage your project and test sessions.';
$projectBase = $project;
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

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>
  <!--begin::Analytics navigation-->
        <div class="card">
            <div class="card-body">
                <ul class="nav mx-auto flex-shrink-0 flex-center flex-wrap border-transparent fs-6 fw-bold">
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase active" href="/index.php?controller=Analysis&action=index&id=<?php echo $project['id']; ?>">📊 Overview</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase" href="/index.php?controller=Analysis&action=tasks&id=<?php echo $project['id']; ?>">📋 Task Success</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase" href="/index.php?controller=Analysis&action=questionnaires&id=<?php echo $project['id']; ?>">📑 Questionnaires</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase" href="/index.php?controller=Analysis&action=sus&id=<?php echo $project['id']; ?>">🧠 SUS</a>
                    </li>
                    <li class="nav-item my-3">
                        <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase" href="/index.php?controller=Analysis&action=participants&id=<?php echo $project['id']; ?>">👥 Participants</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--end::Analytics navigation-->
      
        <!-- Project Analysis -->
        <h3 class="fw-bold my-4">Overview of: <?php echo htmlspecialchars($project['title']); ?></h3>

        <div class="row g-6 mb-6">
            <!-- Total Evaluations -->
            <div class="col-md-4">
                <div class="card bg-light-primary">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold"><?php echo $totalEvaluations; ?></div>
                        <div class="fs-4 text-muted">Evaluations</div>
                    </div>
                </div>
            </div>

            <!-- Total Responses -->
            <div class="col-md-4">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold"><?php echo $totalResponses; ?></div>
                        <div class="fs-4 text-muted">Responses</div>
                    </div>
                </div>
            </div>

            <!-- Average Time -->
            <div class="col-md-4">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <div class="fs-2x fw-bold"><?php echo $avgTime; ?>s</div>
                        <div class="fs-4 text-muted">Avg. Task Time</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUS Summary -->
        <?php if (!empty($susSummary)) : ?>
            <div class="card mb-6">
                <div class="card-header">
                    <h4 class="card-title">System Usability Scale (SUS) Summary</h4>
                </div>
                <div class="card-body">
                    <p class="fs-4">Average SUS Score: <strong><?php echo $susSummary['average']; ?></strong></p>
                    <p>Usability Rating: <strong><?php echo $susSummary['label']; ?></strong></p>
                    <p>Score Variation: <strong><?php echo ucfirst($susSummary['variation']); ?></strong></p>
                    <p>Low scores (<50): <strong><?php echo $susSummary['low']; ?></strong></p>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-warning">No SUS results available yet.</div>
        <?php endif; ?>

        <div class="col-md-6">
            <div class="card border shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Participants Demographics</h4>
                </div>
                <div class="card-body">
                    <p><strong>Total Participants:</strong> <?php echo $totalParticipants; ?></p>
                    <p><strong>Average Age:</strong> <?php echo $averageAge; ?></p>
                    <p><strong>Gender Distribution:</strong></p>
                    <ul>
                        <?php foreach ($genderDistribution as $g): ?>
                            <li><?php echo ucfirst($g['participant_gender'] ?? 'Unspecified'); ?>: <?php echo $g['count']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Education Levels:</strong></p>
                    <ul>
                        <?php foreach ($educationDistribution as $e): ?>
                            <li><?php echo $e['participant_academic_level'] ?? 'Unspecified'; ?>: <?php echo $e['count']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <p><strong>Task Success Rate:</strong> <?php echo $taskSuccessRate; ?>%</p>


        

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
