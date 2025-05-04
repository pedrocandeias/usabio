<!-- app/views/projects/form.php -->

<?php
$menuActive = 'settings';
$pageTitle = 'Projects';
$pageDescription = 'Create a project.';
$title = 'Create a new project';
$headerNavbuttons = [
    __('back_to_projects_list') => [
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
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create a new project</h3>
               
            </div>
            <div class="card-body">

                <form method="POST" action="/index.php?controller=Project&action=store">
                
                    <?php
                    $fields = [
                        'title' => 'Project Title',
                        'description' => 'Project Description',
                        'product_under_test' => 'Product Under Test',
                        'business_case' => 'Business Case',
                        'test_objectives' => 'Test Objectives',
                        'participants' => 'Participants',
                        'equipment' => 'Equipment',
                        'responsibilities' => 'Responsibilities',
                        'location_dates' => 'Location & Dates',
                        'test_procedure' => 'Procedure',
                    ];

                    foreach ($fields as $name => $label): ?>
                        <div class="mb-3">
                            <label for="<?php echo $name; ?>" class="form-label"><?php echo $label; ?></label>
                            <textarea class="form-control" id="<?php echo $name; ?>" name="<?php echo $name; ?>" rows="3" required></textarea>
                        </div>
                    <?php endforeach; ?>


                    <button type="submit" class="btn btn-primary">Create Project</button>
                    <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">Cancel</a>
                </form>
          
        </div>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

