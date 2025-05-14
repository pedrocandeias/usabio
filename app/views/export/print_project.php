<?php
$menuActive = 'print';
$pageTitle = 'Print Project';
$pageDescription = 'Print the projects, tasks, etc.';
$title = 'Print Project:' . htmlspecialchars($project['title']);
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
       


        <div class="card my-4  d-print-none">
            <div class="card-header">
               <h3 class="card-title"><?php echo __('print_project'); ?>: <?php echo htmlspecialchars($project['title']) ?></h3>
            </div>
            <div class="card-body">
                <button class="btn btn-primary m-2 w-100" onclick="window.print()">🖨️ <?php echo __('print'); ?></button>
            </div>
        </div>
    
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">

            <div class="col-lx-12 mb-5">
                <div class="card border-primary ">
                    <div class="row ">
                        <div class="col-md-4">
                            <img src="..." class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h3 class="card-title fs-2 pb-5"><?php echo htmlspecialchars($project['title']) ?></h3>
                                <p class="card-text fs-4"><?php echo nl2br(htmlspecialchars($project['description'])) ?></p>     
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
        

        <?php foreach ($tests as $test): ?>
            <?php if($test['layout_image'] == '') { ?>
                <div class="card my-4">
                    <div class="card-header ">
                        <h4 class="mt-4"><?php echo htmlspecialchars($test['title']) ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="card-test fs-4">
                            <p><?php echo nl2br(htmlspecialchars($test['description'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="card my-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img class="img-fluid rounded shadow-sm" src="/uploads/<?php echo $test['layout_image']; ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($test['title']) ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h4 class="mt-4"><?php echo htmlspecialchars($test['title']) ?></h4>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($test['description'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        
                <?php foreach ($taskGroups as $group): ?>
                    <?php if ($group['test_id'] == $test['id']): ?>
                    <div class="card my-4">
                        <div class="card-header ">
                            <h4 class="mt-4"><?php echo htmlspecialchars($group['title']) ?></h4>
                        </div>
                        <div class="card-body px-0">
                            <div class="container">
                            <?php 
                            $i = 1;
                            foreach ($tasksByGroup[$group['id']] as $task): ?>
                                <div class="row g-5 mb-5">
                                    <div class="col-sm-12 col-md-8">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h3 class="card-title"><?php echo $i++; ?>: <?php echo htmlspecialchars($task['task_text']) ?></h3>
                                                <div class="card-text"> <hr>
                                                    <?php if (!empty($task['script'])) : ?><p class="fs-4"><strong><?php echo __('script');?>:</strong> <?php echo nl2br(htmlspecialchars($task['script'])) ?></p><?php 
                                                    endif; ?>
                                                    <?php if (!empty($task['scenario'])) : ?><p class="fs-4"><strong><?php echo __('scenario'); ?>:</strong> <?php echo nl2br(htmlspecialchars($task['scenario'])) ?></p><?php 
                                                    endif; ?>
                                                    <?php if (!empty($task['metrics'])) : ?><p class="fs-4"><strong><?php echo __('metrics'); ?>:</strong> <?php echo nl2br(htmlspecialchars($task['metrics'])) ?></p><?php 
                                                    endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="card  bg-light">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo __('time'); ?></h5>
                                                <div class="form-group">
                                                    <input type="text" class="form-control">
                                                </div>
                                                <h5 class="card-title my-5"><?php echo __('status'); ?>:</h5>
                                                <?php
                                                $type = $task['task_type'] ?? 'text';
                                                $options = [];

                                                    if (!empty($task['task_options'])) {
                                                        $pairs = explode(';', $task['task_options']);
                                                        foreach ($pairs as $pair) {
                                                            if (strpos($pair, ':') !== false) {
                                                                [$label, $value] = explode(':', $pair, 2);
                                                            } else {
                                                                $label = $value = trim($pair);
                                                            }
                                                            $options[] = ['label' => trim($label), 'value' => trim($value)];
                                                        }
                                                    }

                                                    switch ($type) {
                                                        case 'radio':
                                                            foreach ($options as $opt): ?>
                                                            <div class="form-group">
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" name="answer[<?php echo $task['id']; ?>]" value="" id="r-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black" for="r-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                                        <?php echo htmlspecialchars($opt['label']); ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <?php endforeach;
                                                            break;

                                                        case 'checkbox':
                                                            foreach ($options as $opt): ?>
                                                            <div class="form-group">
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" name="answer[<?php echo $task['id']; ?>][]" value="" id="c-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black" for="c-<?php echo $task['id'] . '-' . $opt['value']; ?>"><?php echo $opt['label']; ?></label>
                                                                </div>
                                                            </div>
                                                            <?php endforeach;
                                                            break;

                                                        case 'dropdown': ?>
                                                        <div class="form-group">
                                                                <?php foreach ($options as $opt): ?>
                                                                    <input class="form-check-input" type="checkbox" name="answer[<?php echo $task['id']; ?>][]" value="" id="c-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black" for="c-<?php echo $task['id'] . '-' . $opt['value']; ?>"><?php echo $opt['label']; ?></label>
                                                                <?php endforeach; ?>
                                                            
                                                        </div>
                                                            <?php
                                                            break;

                                                        default: ?>
                                                        <div class="form-group">
                                                            <textarea name="answer[<?php echo $task['id']; ?>]" class="form-control" rows="3" placeholder="Participant response..."></textarea>
                                                        </div>
                                                            <?php
                                                    }
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-12">
                                        <div class="card  bg-light">
                                            <div class="card-body">
                                            <h5 class="card-title"><?php echo __('observations'); ?></h5>
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>

                                    
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?> 

    
                <?php foreach ($questionnaireGroups as $group): ?>
                    <?php if ($group['test_id'] == $test['id']): ?>
                    <div class="card my-4">
                        <div class="card-header ">
                            <h4 class="mt-4"><?php echo htmlspecialchars($group['title']) ?></h4>
                        </div>
                        <div class="card-body px-0">
                            <div class="container">
                            <?php 
                            $i = 1;
                            foreach ($questionsByGroup[$group['id']] as $question): ?>
                            
                            
                                <div class="row g-5 mb-5">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h3 class="card-title"><?php echo $i++; ?>: <?php echo htmlspecialchars($question['text']) ?></h3>
                                                <div class="card-text"> 
                                                    <hr>
                                                    <?php
                                                    $type = $question['question_type'];
                                                    $options = [];
                                                    if (!empty($question['question_options'])) {
                                                        $pairs = explode(';', $question['question_options']);
                                                        foreach ($pairs as $pair) {
                                                            $pair = trim($pair);
                                                            if (strpos($pair, ':') !== false) {
                                                                [$label, $value] = explode(':', $pair, 2);
                                                            } else {
                                                                $label = $value = $pair;
                                                            }
                                                            $options[] = ['label' => trim($label), 'value' => trim($value)];
                                                        }
                                                    }
                                                    switch ($type):
                                                        case 'radio':
                                                            foreach ($options as $opt): ?>
                                                                <div class="form-check mb-2  form-check-inline">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="answer[<?php echo $question['id']; ?>]"
                                                                        value="<?php echo $opt['value']; ?>"
                                                                        id="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black fs-4"
                                                                        for="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                        <?php echo htmlspecialchars($opt['label']); ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach;
                                                            break;

                                                        case 'checkbox':
                                                            foreach ($options as $opt): ?>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="answer[<?php echo $question['id']; ?>][]"
                                                                        value="<?php echo $opt['value']; ?>"
                                                                        id="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black fs-4"
                                                                        for="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                        <?php echo htmlspecialchars($opt['label']); ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach;
                                                            break;

                                                        case 'dropdown': ?>
                                                            <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="answer[<?php echo $question['id']; ?>][]"
                                                                        value="<?php echo $opt['value']; ?>"
                                                                        id="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                    <label class="form-check-label text-black fs-4"
                                                                        for="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                                        <?php echo htmlspecialchars($opt['label']); ?>
                                                                    </label>
                                                                </div>
                                                            <?php break;

                                                        default: ?>
                                                            <textarea name="answer[<?php echo $question['id']; ?>]"
                                                                    class="form-control" rows="3"
                                                                    placeholder="Participant response..."></textarea>
                                                    <?php endswitch; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        
            <div class="card my-4">
                <div class="card-header ">
                    <h3 class="card-title">👥 <?php echo __('participants'); ?></h3>
                </div>
                <div class="card-body px-0">
                    <div class="container">
                        <div class="row g-5 mb-5">
                            <div class="col-sm-12 col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th><?php echo __('name');?></th>
                                                <th><?php echo __('age');?></th>
                                                <th><?php echo __('gender');?></th>
                                                <th><?php echo __('academic_qualification');?></th>
                                                <?php foreach ($customFields as $field): ?>
                                                    <th><?php echo htmlspecialchars($field['label']) ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($participants as $p): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($p['participant_name']) ?></td>
                                                    <td><?php echo htmlspecialchars($p['participant_age']) ?></td>
                                                    <td><?php echo htmlspecialchars($p['participant_gender']) ?></td>
                                                    <td><?php echo htmlspecialchars($p['participant_academic_level']) ?></td>
                                                    <?php foreach ($customFields as $field): ?>
                                                        <td><?php echo htmlspecialchars($customData[$p['id']][$field['id']] ?? '') ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>