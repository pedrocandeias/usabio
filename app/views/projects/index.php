<!-- app/views/projects/index.php -->
<?php 

if (!isset($project) && isset($this) && property_exists($this, 'project')) $project = $this->project;
if (!isset($testscount) && isset($this) && property_exists($this, 'projectTests')) $testscount = $this->projectTests;
if (!isset($participantscount) && isset($this) && property_exists($this, 'projecParticipants')) $participantsscount = $this->projecParticipants;
if (!isset($assignedUsers) && isset($this) && property_exists($this, 'projecAssignedUsers')) $assignedUsers = $this->projecAssignedUsers;

$menuActive = 'overview';
$pageTitle = 'Projects';
$pageDescription = 'Manage your projects and test sessions.';
$title = 'My Projects';
$headerNavbuttons = [
    'Create New Project' => [
        'url' => '/index.php?controller=Project&action=create',
        'icon' => 'ki-duotone ki-plus fs-2',
        'class' => 'btn bg-body btn-active-color-primary',
        'id' => 'kt_toolbar_primary_button',
        'data' => [
            'bs-toggle' => 'modal',
            'bs-target' => '#kt_modal_create_app',
        ],
    ],
];
							

require __DIR__ . '/../layouts/header.php'; ?>


	<!--begin::Container-->
    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
						<!--begin::Post-->
						<div class="content flex-row-fluid" id="kt_content">
							<!--begin::Stats-->
							<div class="row gx-6 gx-xl-9">
                                <?php if (!empty($projects)) : ?>
                                        
                                <div class="col-lg-6 col-xxl-6">
									<!--begin::Card-->
									<div class="card h-100">
										<!--begin::Card body-->
										<div class="card-body p-9">
                                        
                                            <!--begin::Heading-->
                                            <div class="fs-2hx fw-bold"><?php echo count($projects); ?></div>
											<div class="fs-4 fw-semibold text-gray-500 mb-7">Current Projects</div>
											<!--end::Heading-->
											<!--begin::Wrapper-->
											<div class="d-flex flex-wrap">
												<!--begin::Chart-->
												<div class="d-flex flex-center h-100px w-100px me-9 mb-5">
													<canvas id="kt_project_list_status_chart"></canvas>
												</div>
												<!--end::Chart-->
												<!--begin::Labels-->
												<div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                                    <?php
                                                    $statusCounts = [
                                                        'Completed' => 0,
                                                        'In Progress' => 0,
                                                    ];

                                                    foreach ($projects as $project) {
                                                        if (!empty($project['status']) && isset($statusCounts[$project['status']])) {
                                                            $statusCounts[$project['status']]++;
                                                        }
                                                    }
                                                    ?>

                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                        <div class="bullet bg-success me-3"></div>
                                                        <div class="text-gray-500">Completed</div>
                                                        <div class="ms-auto fw-bold text-gray-700"><?php echo $statusCounts['Completed']; ?></div>
                                                    </div>
                                                    <!--end::Label-->
                                                    <!--begin::Label-->
                                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                        <div class="bullet bg-warning me-3"></div>
                                                        <div class="text-gray-500">In Progress</div>
                                                        <div class="ms-auto fw-bold text-gray-700"><?php echo $statusCounts['In Progress']; ?></div>
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
								<?php endif; ?>
								<div class="col-lg-6 col-xxl-6">
									<!--begin::Clients-->
									<div class="card h-100">
										<div class="card-body p-9">
											<!--begin::Heading-->
											<div class="fs-2hx fw-bold">49</div>
											<div class="fs-4 fw-semibold text-gray-500 mb-7">Teammates</div>
											<!--end::Heading-->
											<!--begin::Users group-->
											<div class="symbol-group symbol-hover mb-9">
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Alan Warden">
													<span class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Michael Eberon">
													<img alt="Pic" src="assets/media/avatars/300-11.jpg" />
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Michelle Swanston">
													<img alt="Pic" src="assets/media/avatars/300-7.jpg" />
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Francis Mitcham">
													<img alt="Pic" src="assets/media/avatars/300-20.jpg" />
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Susan Redwood">
													<span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Melody Macy">
													<img alt="Pic" src="assets/media/avatars/300-2.jpg" />
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Perry Matthew">
													<span class="symbol-label bg-info text-inverse-info fw-bold">P</span>
												</div>
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Barry Walter">
													<img alt="Pic" src="assets/media/avatars/300-12.jpg" />
												</div>
												<a href="#" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
													<span class="symbol-label bg-dark text-gray-300 fs-8 fw-bold">+42</span>
												</a>
											</div>
											<!--end::Users group-->
											<!--begin::Actions-->
											<div class="d-flex">
												<a href="#" class="btn btn-primary btn-sm me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">View all</a>
												<a href="#" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">Add New</a>
											</div>
											<!--end::Actions-->
										</div>
									</div>
									<!--end::Clients-->
								</div>
							</div>
							<!--end::Stats-->
							<!--begin::Toolbar-->
							<div class="d-flex flex-wrap flex-stack my-5">
								<!--begin::Heading-->
								<h2 class="fs-2 fw-semibold my-2">Projects 
								<span class="fs-6 text-gray-500 ms-1">by Status</span></h2>
								<!--end::Heading-->
								<!--begin::Controls-->
								<div class="d-flex flex-wrap my-1">
									<!--begin::Select wrapper-->
									<div class="m-0">
										<!--begin::Select-->
										<select name="status" data-control="select2" data-hide-search="true" class="form-select form-select-sm bg-body border-body fw-bold w-125px">
											<option value="all" selected="selected">All</option>
											<option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
										</select>
										<!--end::Select-->
									</div>
									<!--end::Select wrapper-->
								</div>
								<!--end::Controls-->
							</div>
							<!--end::Toolbar-->
							<!--begin::Row-->
							<div class="row g-6 g-xl-9">
								
                            <?php if (!empty($projects)) : ?>
                                <?php foreach ($projects as $project): ?>
                                <!--begin::Col-->
                                <div class="col-md-6 col-xl-4">
									<!--begin::Card-->
									<a href="/index.php?controller=Project&action=show&id=<?php echo $project['id']; ?>" class="card border-hover-primary">
										<!--begin::Card header-->
										<div class="card-header border-0 pt-9">
											<!--begin::Card Title-->
											<div class="card-title m-0">
												<!--begin::Avatar-->
												<div class="symbol symbol-50px w-50px bg-light">
                                                    <?php if(!empty($project['image'])): ?>
                                                        <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="image" class="p-3" />
                                                    <?php else: ?>
                                                        <img src="assets/media/svg/brand-logos/plurk.svg" alt="image" class="p-3" />
                                                     <?php endif; ?> 
												</div>
												<!--end::Avatar-->
											</div>
											<!--end::Car Title-->
											<!--begin::Card toolbar-->
											<div class="card-toolbar">
                                            <?php if(!empty($project['status'])): ?>
                                             <?php if ($project['status'] == 'completed') : ?>
                                                <span class="badge badge-light-success fw-bold me-auto px-4 py-3">Completed</span>
                                            <?php elseif ($project['status'] == 'in_progress') : ?>
                                                <span class="badge badge-light-warning fw-bold me-auto px-4 py-3">In Progress</span>
                                            <?php else : ?>
                                                <span class="badge fw-bold me-auto px-4 py-3"></span>
                                            <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-light-secondary fw-bold me-auto px-4 py-3">No Status</span>
                                            <?php endif; ?>
                                            
                                            </div>
											<!--end::Card toolbar-->
										</div>
										<!--end:: Card header-->
										<!--begin:: Card body-->
										<div class="card-body p-9">
											<div class="fs-3 fw-bold text-gray-900"><?php echo htmlspecialchars($project['title']); ?></div>
											<p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7"><?php echo htmlspecialchars($project['description']); ?></p>
											<div class="d-flex flex-wrap mb-5">
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                                    <div class="fw-semibold text-gray-500">Created</div>
                                                    <div class="fs-6 text-gray-800 fw-bold">
                                                        <?php echo date('F j, Y', strtotime($project['created_at'])); ?>
                                                    </div>
												</div>
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
													<div class="fs-6 text-gray-800 fw-bold">Update</div>
                                                    <div class="fs-6 text-gray-800 fw-bold">
                                                    <?php echo date('F j, Y', strtotime($project['updated_at'])); ?>
                                                    </div>
												</div>
											</div>
                                        
											<div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="This project 50% completed">
												<div class="bg-primary rounded h-4px" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
											
											<div class="symbol-group symbol-hover">
												<!--begin::User-->
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Emma Smith">
													<img alt="Pic" src="assets/media/avatars/300-6.jpg" />
												</div>
												<!--begin::User-->
												<!--begin::User-->
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Rudy Stone">
													<img alt="Pic" src="assets/media/avatars/300-1.jpg" />
												</div>
												<!--begin::User-->
												<!--begin::User-->
												<div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Susan Redwood">
													<span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
												</div>
												<!--begin::User-->
											</div>
											<!--end::Users-->
                                            <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="This project 50% completed">
                                        </div>
										

                                            
										</div>
										<!--end:: Card body-->
									</a>
									<!--end::Card-->
								</div>
								<!--end::Col-->                
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No projects found.</p>
                            <?php endif; ?>
							</div>
							<!--end::Row-->
					    <?php require __DIR__ . '/../layouts/pagination.php'; ?>
							
						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->

        <!--begin::Modals-->
        <!--begin::Modal - Create App-->
        <div class="modal fade" id="kt_modal_create_app" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2>Create a new project</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-lg-10 px-lg-10">
                        <div class="row g-4">

                            <!-- Custom Project -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">🛠️ Custom Project</h4>
                                        <p class="card-text">Start from scratch and define all project details manually.</p>
                                        <a href="/index.php?controller=Project&action=create" class="btn btn btn-light-primary w-100">Start Manual Setup</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Import Project -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">📦 Import Project</h4>
                                        <p class="card-text">Upload a JSON file exported from this platform containing a complete project.</p>
                                        <a href="/index.php?controller=Import&action=uploadJSONForm" class="btn btn btn-light-primary w-100">Import from File</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Projects -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">🎯 Use a Template</h4>
                                        <p class="card-text">Choose from predefined templates for quick setup (e.g. smart lamp test, onboarding, etc.).</p>
                                        <a href="/index.php?controller=Import&action=chooseTemplate" class="btn btn btn-light-primary w-100">Browse Templates</a>
                                    </div>
                                </div>
                            </div>

                            <!-- AI Generator -->
                            <div class="col-md-6">
                                <div class="card card-flush shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">🤖 AI Generator</h4>
                                        <p class="card-text">Let AI help you generate a project based on a few simple inputs.</p>
                                        <a href="/index.php?controller=Import&action=aiForm" class="btn btn btn-light-primary w-100">
                                            🧠 Generate with AI</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - Create project-->
        <!--end::Modals-->
<?php 
        $totalProjects = count($projects);
        $statusCounts = json_encode($statusCounts);

        $percentages = [];
        foreach (json_decode($statusCounts, true) as $status => $count) {
            $percentages[$status] = $totalProjects > 0 ? round(($count / $totalProjects) * 100, 2) : 0;
        }
       
?>
    <?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

<script>
// Calculate total projects and percentages
"use strict";

// Class definition
var KTProjectList = function () {    
    var initChart = function () {
        // init chart
        var element = document.getElementById("kt_project_list_status_chart");

        if (!element) {
            return;
        }

        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [40, 60,],
                    backgroundColor: ['#00A3FF', '#50CD89']
                }],
                labels: ['In Progress', 'Completed',]
            },
            options: {
                chart: {
                    fontFamily: 'inherit'
                },
                borderWidth: 0,
                cutout: '75%',
                cutoutPercentage: 65,
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: false
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                stroke: {
                    width: 0
                },
                tooltips: {
                    enabled: true,
                    intersect: false,
                    mode: 'nearest',
                    bodySpacing: 5,
                    yPadding: 10,
                    xPadding: 10,
                    caretPadding: 0,
                    displayColors: false,
                    backgroundColor: '#20D489',
                    titleFontColor: '#ffffff',
                    cornerRadius: 4,
                    footerSpacing: 0,
                    titleSpacing: 0
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }                
            }
        };

        var ctx = element.getContext('2d');
        var myDoughnut = new Chart(ctx, config);
    }

    // Public methods
    return {
        init: function () {
            initChart();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTProjectList.init();
});
</script>


</body>
</html>
