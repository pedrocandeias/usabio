<?php
$menuActive = 'settings';
$pageTitle = __('projects');
$pageDescription = __('create_a_new_project');
$title = __('create_a_new_project');
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
       
       

                <form method="POST" action="/index.php?controller=Project&action=store" enctype="multipart/form-data">
                        <div class="row g-5 g-xl-8">

                            <div class="col-xl-12">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <div class="fw-semibold fs-6">
                                            <label for="title" class="form-label fw-bold"><?php echo __('project_title'); ?></label>
                                            <input class="form-control" id="title" name="title" value="" required>
                                        </div>
                                        <div class="fw-semibold fs-6 my-5">
                                            <label for="title" class="form-label fw-bold"><?php echo __('project_description'); ?></label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                        <div class="fw-semibold fs-6 my-5">
                                            <label for="project_image" class="form-label fw-bold"><?php echo __('project_image'); ?></label>
                                            <input type="file" class="form-control" id="project_image" name="project_image" accept="image/*">
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
                                        <p class="text-muted card-text"><?php echo __('whats_being_tested_what_are_the_business_and_experience_goals_of_the_product'); ?></p>
                                        <textarea class="form-control" id="product_under_test" name="product_under_test" placeholder="" required></textarea>
                                    </div>
                                </div>

                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 px-5">
                                        <h3 class="card-title">
                                            <?php echo __('business_case'); ?>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column  px-5">
                                        <p class="text-muted card-text"><?php echo __('why_are_we_doing_this_test_what_are_the_benefits_What_are_the_risks_of_not_testing');?></p>
                                        <textarea class="form-control" id="business_case" name="business_case" placeholder="" required></textarea>
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
                                        <p class="text-muted card-text"><?php echo __('what_are_the_goals_of_the_usability_test_what_specific_questions_will_be_answered_what_hypotheses_will_be_tested')?></p>
                                        <textarea class="form-control h-100" id="test_objectives" rows="5" name="test_objectives" placeholder="" required></textarea>
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
                                        <textarea class="form-control h-100" id="participants" rows="5" name="participants" placeholder="" required></textarea>
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
                                        <textarea class="form-control h-100" id="equipment" rows="5" name="equipment" placeholder="" required></textarea>
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
                                        <p class="text-muted card-text"><?php echo __('who_is_responsible_for_what_what_are_the_roles_and_responsibilities_of_the_team_members'); ?></p>
                                        <textarea class="form-control h-100" id="responsibilities" rows="5" name="responsibilities" placeholder="" required></textarea>
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
                                        <p class="text-muted card-text"><?php echo __('where_will_the_test_take_place_What_are_the_dates_and_times_of_the_test'); ?></p>
                                        <textarea class="form-control h-100" id="location_dates" rows="5" name="location_dates" placeholder="" required></textarea>
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
                                    <p class="text-muted card-text"><?php echo __('what_is_the_procedure_for_the_test_what_are_the_steps_that_will_be_followed_what_are_the_tasks_that_will_be_performed'); ?></p>

                                    <textarea class="form-control h-100" id="test_procedure" rows="5" name="test_procedure" placeholder="" required></textarea>
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
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

