<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
$headerNavbuttons = [
    __('back_to_projects_list') => [
        'url' => '/index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_home_primary_button',
    ],
];

require __DIR__ . '/../layouts/header.php'; 
?>

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">

        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>

        <!--begin::Row-->
        <div class="row g-5 g-xl-8">

            <!--begin::Col (Product under test)-->
            <div class="col-xl-4">
                <div class="card  mb-xl-8 shadow-sm">
                    <div class="card-header py-5 bg-primary">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-3 mb-1 text-white"><?php echo __('product_under_test'); ?></span>
                        </h3>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold fs-6">
                            <?php echo htmlspecialchars($project['product_under_test']); ?>
                        </div>
                    </div>
                </div>
                <div class="card mb-xl-8 shadow-sm">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-3 text-white"><?php echo __('assigned_moderators'); ?></span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($assignedUsers)) : ?>
                            <ul class="list-group mb-4">
                                <?php foreach ($assignedUsers as $user): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($user['username']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted"><?php echo __('no_moderators_assigned_to_this_project'); ?>.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            <!--end::Col-->

            <!--begin::Col (Business case + Test objectives)-->
            <div class="col-xl-8">
                <div class="row gx-5 gx-xl-8 mb-5">

                    <div class="col-xl-4 mb-5 mb-xl-0">
                        <div class="card card-xl-stretch mb-xl-3 shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('test_objectives'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['test_objectives'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card card-xl-stretch shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('business_case'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['business_case'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row gx-5 gx-xl-8 mb-5 mb-xl-8">
                    <!-- Participants and Location/Dates -->
                    <div class="col-xl-6 mb-xl-8">
                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('participants'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['participants'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card card-xl-stretch mb-1 shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('location_dates'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['location_dates'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsibilities and Equipment -->
                    <div class="col-xl-6">
                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('responsabilities'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['responsibilities'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card card-xl-stretch mb-1 shadow-sm">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">
                                    <span class="card-label fw-bold fs-3 text-white"><?php echo __('equipment'); ?></span>
                                </h3>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['equipment'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--end::Col-->

        </div>
        <!--end::Row-->

        <!--begin::Row (Assigned Users and Test Procedures)-->
        <div class="row mb-5 mb-xl-8">

            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8 shadow-sm">

                    <div class="card-header bg-primary">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-3 text-white"><?php echo __('test_procedures'); ?></span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['test_procedure'] ?? __('no_additional_information_available.'))); ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <!--end::Row-->

    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
