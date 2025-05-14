<?php /* footer scripts */ ?>

<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->

<script src="assets/js/widgets.bundle.js"></script>
<script src="assets/js/custom/widgets.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->



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

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>