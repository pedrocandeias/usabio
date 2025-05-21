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
     <?php require_once __DIR__  . '/../layouts/analysis-nav.php'; ?>
       

        <!-- Project Analysis -->
        <h3 class="fw-bold my-10">Overview of: <?php echo htmlspecialchars($project['title']); ?></h3>


        <div class="row g-6 mb-6">
            <div class="col-md-12">
                <?php if (!empty($aiSummary)) : ?>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h4 class="card-title">AI-Powered Usability Summary</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="/index.php?controller=Analysis&action=generateSummaryNow&id=<?php echo $project['id']; ?>">
                                <button class="btn btn-primary mb-4" type="submit">🧠 Regenerate AI Summary</button>
                            </form>

                            <p class="small text-muted">Generated on: <?php echo htmlspecialchars($aiSummary['last_updated']); ?></p>
                            <div class="fs-6"><?php echo nl2br(htmlspecialchars($aiSummary['ai_summary'])); ?></div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info">No AI summary has been generated yet.
                        
                    </div>
  <form method="post" action="/index.php?controller=Analysis&action=generateSummaryNow&id=<?php echo $project['id']; ?>">
    <button type="submit" class="btn btn-primary">Gerar Resumo AI</button>
</form>

                    <?php endif; ?>
            </div>
        </div>

        

        <div class="row g-6 mb-6">


            <!-- Total Evaluations -->
            <div class="col-md-3">
                <div class="card bg-primary">
                    <div class="card-body text-white">
                        <div class="fs-2x fw-bold"><?php echo $totalEvaluations; ?></div>
                        <div class="fs-3">Evaluations</div>
                    </div>
                </div>
            </div>

            <!-- Total Responses -->
            <div class="col-md-3">
                <div class="card bg-secondary">
                    <div class="card-body text-white">
                        <div class="fs-2x fw-bold"><?php echo $totalResponses; ?></div>
                        <div class="fs-3">Responses</div>
                    </div>
                </div>
            </div>

            <!-- Average Time -->
            <div class="col-md-3">
                <div class="card bg-info">
                    <div class="card-body text-white">
                        <div class="fs-2x fw-bold"><?php echo $avgTime; ?>s</div>
                        <div class="fs-3">Avg. Task Time</div>
                    </div>
                </div>
            </div>

            <!-- Average Success Rate -->
            <div class="col-md-3">
                <div class="card bg-success">
                    <div class="card-body text-white">
                        <div class="fs-2x fw-bold"><?php echo $taskSuccessRate; ?>%</div>
                        <div class="fs-3">Task Success Rate:</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-6 mb-6">

            <h3 class="fw-bold my-10"><?php echo __('participant_demografics'); ?></h3>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">
                                    <i class="ki-duotone ki-compass fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <!--end::Symbol-->
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Total Participants</a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $totalParticipants; ?></div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">
                                    <i class="ki-duotone ki-element-11 fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                            </div>
                            <!--end::Symbol-->
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-5 text-gray-800 text-hover-primary fw-bold">Average Age</a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-5 text-gray-800 pe-1"><?php echo $averageAge; ?></div>
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->
                        </div>
                       
                    </div>
                </div>
            </div>

            <?php if ($genderDistribution) : ?>
                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1"><?php echo __('gender_distribution'); ?></span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->

                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="fw-bold text-muted">

                                            <th class="min-w-200px"><?php echo __('gender'); ?></th>
                                            <th class="min-w-150px"><?php echo __('value'); ?></th>
                                            <th class="min-w-150px"></th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                        <?php foreach ($genderDistribution as $g): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-45px me-5">
                                                        <?php
                                                        $gender = strtolower($g['participant_gender'] ?? '');
                                                        $icon = 'fa-genderless';
                                                        $iconColor = 'text-gray-500';

                                                        if ($gender === 'male') {
                                                            $icon = 'fa-mars';
                                                            $iconColor = 'text-primary';
                                                        } elseif ($gender === 'female') {
                                                            $icon = 'fa-venus';
                                                            $iconColor = 'text-danger';
                                                        } elseif ($gender === 'other' || $gender === 'non-binary') {
                                                            $icon = 'fa-genderless';
                                                            $iconColor = 'text-warning';
                                                        }
                                                        ?>
                                                        <span class="symbol-label bg-light">
                                                            <i class="fa-solid <?php echo $icon . ' ' . $iconColor; ?> fs-2"></i>
                                                        </span>
                                                        </div>
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6"><?php echo ucfirst($g['participant_gender'] ?? 'Unspecified'); ?></a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6"><?php echo $g['count']; ?></a>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex flex-column w-100 me-2">
                                                        <div class="d-flex flex-stack mb-2">
                                                            <span class="text-muted me-2 fs-7 fw-bold">50%</span>
                                                        </div>
                                                        <div class="progress h-6px w-100">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--begin::Body-->
                    </div>
                </div>
            <?php endif; ?>


            <?php if ($educationDistribution) : ?>
                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1"><?php echo __('education_level'); ?></span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->

                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="fw-bold text-muted">

                                            <th class="min-w-200px"><?php echo __('level'); ?></th>
                                            <th class="min-w-150px"><?php echo __('value'); ?></th>
                                            <th class="min-w-150px"></th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                          <?php foreach ($educationDistribution as $e): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6"><?php echo $e['participant_academic_level'] ?? 'Unspecified'; ?></a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6"><?php echo $e['count']; ?></a>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex flex-column w-100 me-2">
                                                        <div class="d-flex flex-stack mb-2">
                                                            <span class="text-muted me-2 fs-7 fw-bold">50%</span>
                                                        </div>
                                                        <div class="progress h-6px w-100">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--begin::Body-->
                    </div>
                </div>
            <?php endif; ?>
            
   

            <!-- SUS Summary -->
            <?php if (!empty($susSummary)) : ?>


                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1"><?php echo __('system_usability_scale'); ?></span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->

                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <!--begin::Table head-->
                                    <thead>
                                        <tr class="fw-bold text-muted">

                                            <th class="min-w-200px"><?php echo __('Metric'); ?></th>
                                            <th class="min-w-150px"><?php echo __('value'); ?></th>
                                   
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">Average SUS Score</a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6">
                                                    <?php
                                                        $avg = $susSummary['average'];
                                                        if ($avg >= 85) {
                                                            $label = 'Excellent';
                                                            $badgeClass = 'badge-light-success';
                                                        } elseif ($avg >= 70) {
                                                            $label = 'Good';
                                                            $badgeClass = 'badge-light-success';
                                                        } elseif ($avg >= 50) {
                                                            $label = 'OK';
                                                            $badgeClass = 'badge-light-warning';
                                                        } else {
                                                            $label = 'Poor';
                                                            $badgeClass = 'badge-light-danger';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?> fs-6 fw-bold ms-2">
                                                        <?php echo $avg; ?>
                                                    </span>
                                                </a>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">Usability Rating</a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6">
                                                    <?php
                                                        $avg = $susSummary['average'];
                                                        if ($avg >= 85) {
                                                            $label = 'Excellent';
                                                            $badgeClass = 'badge-light-success';
                                                        } elseif ($avg >= 70) {
                                                            $label = 'Good';
                                                            $badgeClass = 'badge-light-success';
                                                        } elseif ($avg >= 50) {
                                                            $label = 'OK';
                                                            $badgeClass = 'badge-light-warning';
                                                        } else {
                                                            $label = 'Poor';
                                                            $badgeClass = 'badge-light-danger';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?> fs-6 fw-bold">
                                                        <?php echo $label; ?>
                                                    </span>
                                                </a>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">Score Variation</a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6">
                                                    
                                            <?php
                                                $variation = strtolower($susSummary['variation']);
                                                $badgeClass = 'badge-light-secondary';
                                                if ($variation === 'low') {
                                                    $badgeClass = 'badge-light-danger';
                                                } elseif ($variation === 'medium') {
                                                    $badgeClass = 'badge-light-warning';
                                                } elseif ($variation === 'high') {
                                                    $badgeClass = 'badge-light-success';
                                                }
                                                ?>
                                      <tr>
                                            <td>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">Low scores (50)</a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6"><?php echo $susSummary['low']; ?></a>
                                            </td>
                                            
                                        </tr>          <span class="badge <?php echo $badgeClass; ?> fs-6 fw-bold">
                                                    <?php echo ucfirst($susSummary['variation']); ?>
                                                </span>
                                            </a>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-6">Low scores (50)</a>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary d-block fs-6"><?php echo $susSummary['low']; ?></a>
                                            </td>
                                            
                                        </tr>

                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--begin::Body-->
                    </div>
                </div>
            
                
            <?php else : ?>
                <div class="alert alert-warning">No SUS results available yet.</div>
            <?php endif; ?>

            <?php if (!empty($problematicTasks)): ?>
                <div class="col-md-4">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h4 class="card-title">Potential Usability Issues</h4>
                        </div>
                        <div class="card-body">    
                            <?php if (!empty($problematicTasks)): ?>
                                <div class="alert alert-danger mb-5">
                                    <strong>⚠️ Attention:</strong> The following tasks show potential usability issues:
                                    <ul class="mb-0">
                                        <?php foreach ($problematicTasks as $p): ?>
                                            <li>
                                                <strong><?php echo htmlspecialchars($p['task_text']); ?>:</strong>
                                                <?php echo implode('; ', $p['reasons']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>


</body>

</html>