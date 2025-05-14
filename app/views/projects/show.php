<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
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

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">

        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>

        <!--begin::Row-->
        <div class="row g-5 g-xl-8">

            <!--begin::Col (Product under test)-->
            <div class="col-xl-3">
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm">
                     <div class="card-body d-flex flex-column">
                    <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('product_under_test'); ?>:
                        </h3>    
                    <div class="card-text fs-6">
                            <?php echo htmlspecialchars($project['product_under_test']); ?>
                        </div>
                    </div>
                </div>
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm">
                    <div class="card-body d-flex flex-column">
                    <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('business_case'); ?>:
                        </h3>
                        <div class="card-text fs-6">
                            <?php echo nl2br(htmlspecialchars($project['business_case'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col (Test objectives)-->
            <div class="col-xl-3 mb-5 mb-xl-0">
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('test_objectives'); ?>
                        </h3>    
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['test_objectives'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Col-->   

                
            <!-- Participants and equipment -->
            <div class="col-xl-3 mb-xl-8">
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm ">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('participants'); ?>
                        </h3>
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['participants'])); ?>
                        </div>
                    </div>
                </div>
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm ">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('equipment'); ?>
                        </h3>
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['equipment'])); ?>
                        </div>
                    </div>
                </div>                
            </div>

            <!--end::Col-->
            <div class="col-xl-3">
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm ">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('location_dates'); ?>
                        </h3>
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['location_dates'])); ?>
                        </div>
                    </div>
                </div>

                   
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm ">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('responsabilities'); ?>
                        </h3>
                        <div class="fw-semibold fs-6">
                            <?php echo nl2br(htmlspecialchars($project['responsibilities'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row -->
        <div class="row mb-5 my-xl-8">
            <div class="col-xl-12">
                <div class="card bg-transparent border-primary mb-xl-8 shadow-sm ">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title fs-3 fw-bold text-primary">
                            <?php echo __('test_procedures'); ?>
                        </h3>
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
