<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
$headerNavbuttons = [
    'Back to projects list' => [
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
            <!--begin::Col-->
            <div class="col-xl-4">
                <!--begin::text-->
                <div class="card card-xl-stretch mb-xl-3  shadow-sm">
                    <!--begin::Beader-->
                    <div class="card-header py-5 bg-primary">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-3 mb-1 text-white">Product under test</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold fs-6">
                            <?php echo htmlspecialchars($project['product_under_test']); ?>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::text-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-8">
                <!--begin::Row-->
                <div class="row gx-5 gx-xl-8 mb-5">
                    <div class="col-xl-4 mb-5 mb-xl-0">
                            <!--begin::text-->
                        <div class="card card-xl-stretch mb-xl-3 shadow-sm">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Test Objectives</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                            <div class="fw-semibold fs-6">
                            
                            <?php echo nl2br(htmlspecialchars($project['test_objectives'])); ?>
    
                            </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                    </div>
                    <div class="col-xl-8">
                            <!--begin::text-->
                            <div class="card card-xl-stretch shadow-sm">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary bg-primary ">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Business case</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                            <div class="fw-semibold fs-6">
                            
                            <?php echo nl2br(htmlspecialchars($project['business_case'])); ?>
    
                            </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                    </div>
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row gx-5 gx-xl-8 mb-5 mb-xl-8">
                    <!--begin::Col-->
                    <div class="col-xl-6 mb-xl-8">
                        <!--begin::text-->
                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Participants</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                            <div class="fw-semibold fs-6">
                            
                            <?php echo nl2br(htmlspecialchars($project['participants'])); ?>
    
                            </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                        
                        
                                <!--begin::text-->
                            <div class="card card-xl-stretch mb-1">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary shadow-sm">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Location & dates</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                            <div class="fw-semibold fs-6">
                            
                            <?php echo nl2br(htmlspecialchars($project['location_dates'])); ?>
    
                            </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                        
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xl-6">
                        <!--begin::text-->
                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary ">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Responsabilities</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['responsibilities'])); ?>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->

                        <!--begin::text-->
                        <div class="card card-xl-stretch mb-1 shadow-sm">
                            <!--begin::Beader-->
                            <div class="card-header bg-primary ">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Equipment</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['equipment'])); ?>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Col-->
            <!--begin::Row-->
            <div class="rowmb-5 mb-xl-8">
                    <!--begin::Col-->
                    <div class="col-xl-12 mb-xl-8">
                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                                   <!-- Assigned Users + Procedure -->
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Assigned moderators</h5>
                        <?php if (!empty($assignedUsers)) : ?>
                            <ul class="list-group mb-4">
                                <?php foreach ($assignedUsers as $user): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($user['username']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No moderators assigned to this project.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
        </div> <!--begin::Header-->
                            <div class="card-header bg-primary">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 text-white">Tests procedures</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body d-flex flex-column">
                                <div class="fw-semibold fs-6">
                                    <?php echo nl2br(htmlspecialchars($project['test_procedure'] ?? 'No additional information available.')); ?>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::text-->
                    </div>
                    <!--end::Col-->
                </div> 
                <!--end::Row-->
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

