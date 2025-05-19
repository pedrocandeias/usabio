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
        <h3 class="fw-bold my-10">Overview of: <?php echo htmlspecialchars($project['title']); ?></h3>


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
                        <!--end::Item-->
    
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

            <div class="col-md-4">
            	<!--begin::Card-->
									<div class="card h-100">
										<!--begin::Card body-->
										<div class="card-body p-9">
											<!--begin::Heading-->
											<div class="fs-2hx fw-bold">237</div>
											<div class="fs-4 fw-semibold text-gray-500 mb-7">Current Projects</div>
											<!--end::Heading-->
											<!--begin::Wrapper-->
											<div class="d-flex flex-wrap">
												<!--begin::Chart-->
												<div class="d-flex flex-center h-100px w-100px me-9 mb-5">
													<canvas id="kt_project_list_chart"></canvas>
												</div>
												<!--end::Chart-->
												<!--begin::Labels-->
												<div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-semibold align-items-center mb-3">
														<div class="bullet bg-primary me-3"></div>
														<div class="text-gray-500">Active</div>
														<div class="ms-auto fw-bold text-gray-700">30</div>
													</div>
													<!--end::Label-->
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-semibold align-items-center mb-3">
														<div class="bullet bg-success me-3"></div>
														<div class="text-gray-500">Completed</div>
														<div class="ms-auto fw-bold text-gray-700">45</div>
													</div>
													<!--end::Label-->
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-semibold align-items-center">
														<div class="bullet bg-gray-300 me-3"></div>
														<div class="text-gray-500">Yet to start</div>
														<div class="ms-auto fw-bold text-gray-700">25</div>
													</div>
													<!--end::Label-->
												</div>
												<!--end::Labels-->
											</div>
											<!--end::Wrapper-->
										</div>
										<!--end::Card body-->
									</div>
									<!--end::Card-->
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
        

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
