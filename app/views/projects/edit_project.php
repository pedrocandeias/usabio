<!-- app/views/projects/form.php -->

<?php

$menuActive = 'settings';
$pageTitle = 'Project Settings';
$pageDescription = 'Manage your projects settings.';
$title = 'Project settings';
$headerNavbuttons = [
    __('back_to_projects') => [
        'url' => '/index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_home_primary_button',
    ],
];                        

require __DIR__ . '/../layouts/header.php'; ?>

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>
       
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label"><?php echo __('edit_project'); ?></h3>
                </div>
                <div class="card-toolbar">

                <button type="button" class="btn btn-sm btn-icon btn-color-light-dark btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="ki-duotone ki-element-plus fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </button>
                        <!--begin::Menu 3-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                            <!--begin::Heading-->
                            <div class="menu-item px-3">
                                <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase"><?php echo __('settings'); ?></div>
                            </div>
                            <!--end::Heading-->
                            <div class="separator my-2"></div>
                            <!--end:Menu item-->
                            <!--begin::Menu item-->
             
                           
                            <div class="menu-item px-3">
                                <a href="/index.php?controller=Duplicate&action=selectProject" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_project'); ?></a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="/index.php?controller=Project&action=destroy&id=<?php echo $project['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('are_you_sure_you_want_to_delete_this_project?');?>');"><?php echo __('delete_project'); ?></a>
                            </div>         
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu 3-->                       

                </div>
            </div>
            <div class="card-body">

                    <form method="POST" action="/index.php?controller=Project&action=<?php echo $project['id'] ? 'update' : 'store'; ?>" enctype="multipart/form-data">
                        <?php if ($project['id']): ?>
                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <?php endif; ?>

                        <div class="row g-5 g-xl-8">

                            <div class="col-xl-12">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <div class="fw-semibold fs-6">
                                            <label for="title" class="form-label fw-bold"><?php echo __('project_title'); ?></label>
                                            <input class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                                        </div>
                                        <div class="fw-semibold fs-6 my-5">
                                            <label for="title" class="form-label fw-bold"><?php echo __('project_description'); ?></label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                                        </div>
                                        <div class="fw-semibold fs-6 my-5">
                                            <label for="project_image" class="form-label fw-bold"><?php echo __('project_image'); ?></label>
                                            <input type="file" class="form-control" id="project_image" name="project_image" accept="image/*">
                                            <?php 
                                            if ($project['project_image']): 
                                            ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#projectImageModal">
                                                <img src="/uploads/<?php echo htmlspecialchars($project['project_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
                                            </a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="projectImageModal" tabindex="-1" aria-labelledby="projectImageModalLabel" aria-hidden="true">
                                              <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title" id="projectImageModalLabel"><?php echo htmlspecialchars($project['title']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('close'); ?>"></button>
                                                  </div>
                                                  <div class="modal-body text-center">
                                                    <img src="/uploads/<?php echo htmlspecialchars($project['project_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                             </div>
                             
                            <!--begin::Col (Product under test)-->
                            <div class="col-xl-3">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('product_under_test'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">Whats being tested? What are the business and experience goals of the product?</p>
                                        <textarea class="form-control" id="product_under_test" name="product_under_test" placeholder="<?php echo __('product_under_test'); ?>" required><?php echo htmlspecialchars($project['product_under_test']); ?></textarea>
                                    </div>
                                </div>

                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('business_case'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">Why are we doing this test? What are the benefits? What are the risks of not testing?</p>
                                        <textarea class="form-control" id="business_case" name="business_case" placeholder="<?php echo __('business_case'); ?>" required><?php echo htmlspecialchars($project['business_case']); ?></textarea>
                                    </div>
                                </div> 
                            </div>
                            <!--end::Col-->

                            <!--begin::Col -->
                            <div class="col-xl-3">
                                <div class="card  mb-xl-8 shadow-sm h-100">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('test_objectives'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">What are the goals of the usability test? What specific questions will be answered? What hypotheses will be tested?</p>
                                        <textarea class="form-control h-100" id="test_objectives" rows="5" name="test_objectives" placeholder="<?php echo __('test_objectives'); ?>" required><?php echo htmlspecialchars($project['test_objectives']); ?></textarea>
                                    </div>
                                </div>
                            </div>


                             <!--begin::Col -->
                             <div class="col-xl-3">
                                <!-- begin::Card participants -->
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('participants'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">Who are the participants? What are their characteristics? How many participants will be recruited?</p>
                                        <textarea class="form-control h-100" id="participants" rows="5" name="participants" placeholder="<?php echo __('participants'); ?>" required><?php echo htmlspecialchars($project['participants']); ?></textarea>
                                    </div>
                                </div>
                                <!-- end::Card participants -->
                                <!-- begin::Card equipment -->
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('equipment'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">What equipment is needed for the test? What software or hardware will be used?</p>
                                        <textarea class="form-control h-100" id="equipment" rows="5" name="equipment" placeholder="<?php echo __('equipment'); ?>" required><?php echo htmlspecialchars($project['equipment']); ?></textarea>
                                    </div>
                                </div>
                                <!-- end::Card equipment -->     
                            </div>
                            <!--end::Col -->
                            <!--begin::Col -->
                            <div class="col-xl-3">
                                <!-- begin::Card responsibilities -->
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('responsibilities'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">Who is responsible for what? What are the roles and responsibilities of the team members?</p>
                                        <textarea class="form-control h-100" id="responsibilities" rows="5" name="responsibilities" placeholder="<?php echo __('responsibilities'); ?>" required><?php echo htmlspecialchars($project['responsibilities']); ?></textarea>
                                    </div>
                                </div>
                                <!-- end::Card responsibilities -->
                                <!-- begin::Card location_dates -->
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('location_dates'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text">Where will the test take place? What are the dates and times of the test?</p>
                                        <textarea class="form-control h-100" id="location_dates" rows="5" name="location_dates" placeholder="<?php echo __('location_dates'); ?>" required><?php echo htmlspecialchars($project['location_dates']); ?></textarea>
                                    </div>
                                </div>
                                <!-- end::Card location_dates -->
                            </div>
                            <!--end::Col -->
                        </div>
                        <!--end::Row-->
                        <div class="row g-5 g-xl-8 my-5">
                            <div class="col-xl-12">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('test_procedures'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                    <p class="text-muted card-text">What is the procedure for the test? What are the steps that will be followed? What are the tasks that will be performed?</p>

                                    <textarea class="form-control h-100" id="test_procedure" rows="5" name="test_procedure" placeholder="<?php echo __('test_procedure'); ?>" required><?php echo htmlspecialchars($project['test_procedure']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Row-->



                        
                        <button type="submit" class="btn btn-primary">Update Project</button>
                        <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

