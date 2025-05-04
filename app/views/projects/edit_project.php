<!-- app/views/projects/form.php -->

<?php

$menuActive = 'settings';
$pageTitle = 'Projects';
$pageDescription = 'Manage your projects and test sessions.';
$title = 'Projects settings';
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
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>
       
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Project details</h3>
                </div>
                <div class="card-toolbar">
                    <a class="btn btn-primary" href="/index.php?controller=Duplicate&action=selectProject"class="dropdown-item" >
                        Duplicate a Project
                    </a>
                    <a  class="btn btn-danger" href="/index.php?controller=Project&action=destroy&id=<?php echo $project['id']; ?>"     
                        onclick="return confirm('Are you sure you want to delete this project?');">
                            Delete Project
                    </a>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">Edit Project</h5>
                    <form method="POST" action="/index.php?controller=Project&action=<?php echo $project['id'] ? 'update' : 'store'; ?>">
                        <?php if ($project['id']): ?>
                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <?php endif; ?>

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
                                <textarea class="form-control" id="<?php echo $name; ?>" name="<?php echo $name; ?>" rows="3" required><?php echo htmlspecialchars($project[$name]); ?></textarea>
                            </div>
                        <?php endforeach; ?>


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

