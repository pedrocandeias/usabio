<!-- app/views/projects/index.php -->
<?php 

if (!isset($project) && isset($this) && property_exists($this, 'project')) { $project = $this->project;
}
if (!isset($testscount) && isset($this) && property_exists($this, 'projectTests')) { $testscount = $this->projectTests;
}
if (!isset($participantscount) && isset($this) && property_exists($this, 'projecParticipants')) { $participantsscount = $this->projecParticipants;
}
if (!isset($assignedUsers) && isset($this) && property_exists($this, 'projecAssignedUsers')) { $assignedUsers = $this->projecAssignedUsers;
}

$menuActive = 'overview';
$pageTitle = 'Projects';
$pageDescription = 'Manage your projects and test sessions.';
$title = __('my_projects');

require __DIR__ . '/../layouts/header.php'; ?>

    <!--begin::Container-->
    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
            <!--begin::Post-->
            <div class="content flex-row-fluid" id="kt_content">

                
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
                                        <?php if(!empty($project['project_image'])) : ?>
                                            <img src="/uploads/<?php echo htmlspecialchars($project['project_image']); ?>" alt="image" class="p-3 img-fluid" />
                                        <?php endif; ?> 
                                    </div>
                                    <!--end::Avatar-->
                                </div>
                                <!--end::Car Title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                <?php if(!empty($project['status'])) : ?>
                                    <?php if ($project['status'] == 'completed') : ?>
                                    <span class="badge badge-light-success fw-bold me-auto px-4 py-3"><?php echo __('completed'); ?></span>
                                <?php elseif ($project['status'] == 'in_progress') : ?>
                                    <span class="badge badge-light-warning fw-bold me-auto px-4 py-3"><?php echo __('in_progress'); ?></span>
                                <?php else : ?>
                                    <span class="badge fw-bold me-auto px-4 py-3"></span>
                                <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-light-danger fw-bold me-auto px-4 py-3"><?php echo __('no_status'); ?></span>
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
                                        <div class="fw-semibold text-gray-500"><?php echo __('created'); ?></div>
                                        <div class="fs-6 text-gray-800 fw-bold">
                                            <?php echo date('F j, Y', strtotime($project['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                        <div class="fw-semibold text-gray-500"><?php echo __('updated'); ?></div>
                                        <div class="fs-6 text-gray-800 fw-bold">
                                        <?php echo date('F j, Y', strtotime($project['updated_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <!--end:: Card body-->
                        </a>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->                
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="/assets/media/illustrations/sigma-1/1.png" alt="No projects found" class="mw-100 mb-10" />
                                <h2 class="fs-1"><?php echo __('no_projects_found'); ?>..</h2>
                            </div>
                        </div>
                    </div>

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
                        <h2><?php echo __('create_a_new_project'); ?></h2>
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
                                        <h4 class="card-title">🛠️ <?php echo __('custom_project'); ?></h4>
                                        <p class="card-text"><?php echo __('start_from_scratch_and_define_all_project_details_manually'); ?></p>
                                        <a href="/index.php?controller=Project&action=create" class="btn btn btn-light-primary w-100"><?php echo __('start_manual_setup'); ?></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Import Project -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">📦 <?php echo __('import_project'); ?></h4>
                                        <p class="card-text"><?php echo __('upload_a_json_file_exported_from_this_platform_containing_a_complete_project'); ?></p>
                                        <a href="/index.php?controller=Import&action=uploadJSONForm" class="btn btn btn-light-primary w-100"><?php echo __('import_from_file'); ?></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Projects -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">🎯 <?php echo __('use_a_template'); ?></h4>
                                        <p class="card-text"><?php echo __('choose_from_predefined_templates_for_quick_setup_e_g_smart_lamp_test_onboarding_etc'); ?>Choose from predefined templates for quick setup (e.g. smart lamp test, onboarding, etc.).</p>
                                        <a href="/index.php?controller=Import&action=chooseTemplate" class="btn btn btn-light-primary w-100"><?php echo __('browse_templates'); ?></a>
                                    </div>
                                </div>
                            </div>

                            <!-- AI Generator -->
                            <div class="col-md-6">
                                <div class="card card-flush shadow-sm h-100">
                                    <div class="card-body">
                                        <h4 class="card-title">🤖 <?php echo __('ai_generator'); ?></h4>
                                        <p class="card-text"><?php echo __('let_ai_help_you_generate_a_custom_projectroject_based_on_a_few_simple_inputs'); ?></p>
                                        <a href="/index.php?controller=Import&action=aiForm" class="btn btn btn-light-primary w-100">
                                            🧠 <?php echo __('generate_with_ai'); ?></a>
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

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
