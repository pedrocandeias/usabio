<?php

$title = 'Test detail:'.$test['title'];

$pageTitle = 'Test detail:'.$test['title'];
$pageDescription = 'Manage test tasks and questions.';
$menuActive = 'tests';
$headerNavbuttons = [
    'Back to tests list' => [
        'url' => '/index.php?controller=Test&action=index&project_id=' . $test['project_id'],
        'icon' => 'ki-duotone ki-black-left  fs-2',
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
       
        <div class="d-flex flex-wrap flex-stack">
            <!--begin::Heading-->
            <h3 class="fw-bold mx-4"><?php echo __('tasks_questions_for'); ?> <?php echo $test['title'];?></h3>
            <!--end::Heading-->
        </div>
       
        <div class=" flex-wrap flex-stack pt-10 pb-8 px-2">
            <div class="card ">
                <div class="card-header">
        
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x nav-stretch  border-0">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold fs-3" data-bs-toggle="tab" href="#testdescription"><?php echo __('test_description'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold fs-3" data-bs-toggle="tab" href="#taskgroup"><?php echo __('task_groups_tasks'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold fs-3" data-bs-toggle="tab" href="#questionnairegroup"><?php echo __('questionnaire_groups_questions'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="testTabContent">
                        <div class="tab-pane fade show active" id="testdescription" role="tabpanel">
                        <!--begin::Heading-->
                            <h3 class="fw-bold my-2"><?php echo $test['title'];?></h3>
                            <!--end::Heading-->
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="fs-4"><?php echo htmlspecialchars($test['description']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($test['layout_image'])) : ?>
                                    <div class="mb-5 text-center">
                                        <a href="uploads/<?php echo htmlspecialchars($test['layout_image']); ?>">
                                            <img src="uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" alt="Layout image" class="img-fluid rounded shadow-sm">
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>   
                        </div>

                        <div class="tab-pane fade" id="taskgroup" role="tabpanel">
                        <!-- Task Groups -->
                        <?php if (!empty($taskGroups)) : ?>
                            <div id="task-group-list">
                                <?php foreach ($taskGroups as $group): ?>
                                    <div class="card mb-4 shadow-sm task-group" id="taskgroup<?php echo $group['id']; ?>" data-id="<?php echo $group['id']; ?>">
                                        <div class="card-header d-flex justify-content-between align-items-center p-4">
                                            <div class="d-flex align-items-center">
                                                <div style="cursor: grab;"> 
                                                    <i class="ki-duotone ki-abstract-14 fs-2x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                                <div class="mx-4">
                                                    <h3 class="text-start"><?php echo htmlspecialchars($group['title']); ?></h3>
                                                </div>
                                            </div>   
                                            <div class="d-flex text-end">
                                                <a href="/index.php?controller=Task&action=create&group_id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary"  data-bs-toggle="modal" data-bs-target="#kt_modal_add_task<?php echo $group['id']; ?>">+ <?php echo __('add_new_task'); ?></a>
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
                                                        <a href="/index.php?controller=Response&action=exportCsvByTaskGroup&group_id=<?php echo $group['id']; ?>" class="menu-link bg-outline-warning px-3">📥 <?php echo __('export_answers'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=TaskGroup&action=edit&id=<?php echo $group['id']; ?>" class="menu-link bg-outline-info px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_task_group<?php echo $group['id']; ?>"><?php echo __('edit_task_group'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=TaskGroup&action=duplicate&id=<?php echo $group['id']; ?>" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_task_group'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=TaskGroup&action=destroy&id=<?php echo $group['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('are_you_sure_you_want_to_delete_this_task_group?'); ?>"><?php echo __('delete_task_group'); ?></a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->                       
                                            </div>
                                        </div>

                                        <ul class="list-group list-group-flush task-list" data-group-id="<?php echo $group['id']; ?>">
                                            <?php if (!empty($group['tasks'])) : ?>
                                                <?php foreach ($group['tasks'] as $task): ?>
                                                    <li class="list-group-item d-flex justify-content-between task-item draggable" data-id="<?php echo $task['id']; ?>">
                                                        <div class="d-flex align-items-center">
                                                            <div style="cursor: grab;"> 
                                                                <i class="ki-duotone ki-abstract-14 fs-2x">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </div>
                                                            <div class="mx-4">
                                                                <h4 class="text-start"><?php echo htmlspecialchars($task['task_text']); ?></h4>
                                                            </div>
                                                        </div>   
                                                       
                                                        <div class="text-end">
                                                            <a href="#" class="btn btn-sm btn-secondary"  data-bs-toggle="modal" data-bs-target="#kt_modal_view_task<?php echo $task['id']; ?>"><?php echo __('view_task'); ?></a>
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
                                                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase text-start"><?php echo __('settings'); ?></div>
                                                                </div>
                                                                <!--end::Heading-->
                                                                <div class="separator my-2"></div>
                                                                <!--end:Menu item-->
                                                                <!--begin::Menu item-->
                                                            
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Task&action=edit&id=<?php echo $task['id']; ?>" class="menu-link bg-outline-info px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_task<?php echo $task['id']; ?>"><?php echo __('edit_task'); ?></a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Task&action=duplicate&id=<?php echo $task['id']; ?>" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_task'); ?></a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Task&action=destroy&id=<?php echo $task['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('Are you sure you want to delete this test?');"><?php echo __('delete_task'); ?></a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                            
                                                            </div>
                                                            <!--end::Menu 3-->                       

                                                        </div>
                                                    </li>
                                                     <!--begin::Modal - Create edit/new task -->
                                                        <div class="modal fade" id="kt_modal_edit_task<?php echo $task['id']; ?>" tabindex="-1" aria-hidden="true">
                                                            <!--begin::Modal dialog-->
                                                            <div class="modal-dialog modal-dialog-centered mw-900px">
                                                                <!--begin::Modal content-->
                                                                <div class="modal-content">
                                                                    <!--begin::Modal header-->
                                                                    <div class="modal-header">
                                                                        <!--begin::Modal title-->
                                                                        <h2><?php echo __('edit_task'); ?></h2>
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
                                                                    <form method="POST" action="/index.php?controller=Task&action=<?php echo $task['id'] ? 'update' : 'store'; ?>">
                                                                        <?php if ($task['id']) : ?>
                                                                            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                                                        <?php endif; ?>
                                                                        <input type="hidden" name="task_group_id" value="<?php echo $group['id']; ?>">

                                                                        <div class="row mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Task Text</label>
                                                                                <textarea name="task_text" class="form-control" required rows="4"><?php echo htmlspecialchars($task['task_text']); ?></textarea>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Scenario</label>
                                                                                <textarea name="scenario" class="form-control" rows="4"><?php echo htmlspecialchars($task['scenario']); ?></textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Script (what moderator should say)</label>
                                                                                <textarea name="script" class="form-control" rows="3"><?php echo htmlspecialchars($task['script']); ?></textarea>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Metrics (what to observe)</label>
                                                                                <textarea name="metrics" class="form-control" rows="3"><?php echo htmlspecialchars($task['metrics']); ?></textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Type of Evaluation</label>
                                                                                <select name="task_type" id="task_type" class="form-select">
                                                                                    <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                                                                                        <option value="<?php echo $type; ?>" <?php echo $task['task_type'] === $type ? 'selected' : ''; ?>>
                                                                                            <?php echo ucfirst($type); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Predefined Evaluation Type (optional)</label>
                                                                                <select class="form-select" id="preset-options">
                                                                                    <option value="">— Select a common type —</option>
                                                                                    <option value="Completed:completed;Incompleted:incomplete">Completed/Incompleted</option>
                                                                                    <option value="Yes:yes;No:no">Yes / No</option>
                                                                                    <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1-5)</option>
                                                                                    <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1-5)</option>
                                                                                    <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1-5)</option>
                                                                                </select>
                                                                                <small class="form-text text-muted">Choosing one will auto-fill the options below.</small>
                                                                            </div>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label">Evaluation Options</label>
                                                                            <textarea name="task_options" id="task_options" class="form-control" rows="3"><?php echo htmlspecialchars($task['task_options']); ?></textarea>
                                                                            <small class="form-text text-muted">Use the format: <code>Label:Value;Label:Value;</code></small>
                                                                        </div>

                                                                        
                                                                            <input type="hidden" name="position" class="form-control" value="<?php echo $task['position']; ?>">
                                                                        
                                                                        <div class="d-flex gap-2 mt-4">
                                                                            <input type="hidden" name="preset_type" id="preset_type" value="">
                                                                            <button type="submit" class="btn btn-primary"><?php echo __('save_task'); ?></button>
                                                                            <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!--end::Modal body-->
                                                            </div>
                                                            <!--end::Modal content-->
                                                        </div>
                                                        <!--end::Modal dialog-->
                                                    </div>
                                                    <!--end::Modal - Create new task-->


                                                    <!--begin::Modal - view task -->
                                                    <div class="modal fade" id="kt_modal_view_task<?php echo $task['id']; ?>" tabindex="-1" aria-hidden="true">
                                                        <!--begin::Modal dialog-->
                                                        <div class="modal-dialog modal-dialog-centered mw-900px">
                                                            <!--begin::Modal content-->
                                                            <div class="modal-content">
                                                                <!--begin::Modal header-->
                                                                <div class="modal-header">
                                                                    <!--begin::Modal title-->
                                                                    <h2><?php echo __('view_task'); ?></h2>
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
                                                                <div class="modal-body py-lg-10 px-lg-10 fs-3">
                                                                    
                                                                    <div class="row mb-3 gx-5">
                                                                        <div class="col-md-6 border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('task_text'); ?></p>
                                                                            <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                                                                        </div>
                                                                        <div class="col-md-6  border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('scenario'); ?></p>
                                                                            <p><?php echo htmlspecialchars($task['scenario']); ?></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3">
                                                                        <div class="col-md-6 border border-1 border-gray-300 rounded p-3 ">
                                                                            <p class="fw-bold"><?php echo __('script_what_moderator_should_say'); ?></p>
                                                                            <p><?php echo htmlspecialchars($task['script']); ?></p>
                                                                        </div>
                                                                        <div class="col-md-6 border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('metrics_what_to_observe'); ?></p>
                                                                            <p><?php echo htmlspecialchars($task['metrics']); ?></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3  border border-1 border-gray-300 rounded p-3">
                                                                        <div class="col-md-6">
                                                                            <p class="fw-bold"><?php echo __('type_of_evaluation'); ?>:</p>
                                                                            <p><?php echo $task['task_type']; ?></p>
                                                                        </div>

                
                                                                    </div>

                                                                    <div class="mb-3 border border-1 border-gray-300 rounded p-3">
                                                                        <p class="fw-bold"><?php echo __('evaluation_options'); ?></p>
                                                                        <p><?php echo htmlspecialchars($task['task_options']); ?></p>
                                                                    </div>

                                                                
                                                            </div>
                                                            <!--end::Modal body-->
                                                        </div>
                                                        <!--end::Modal content-->
                                                    </div>
                                                    <!--end::Modal dialog-->
                                                </div>
                                                <!--end::Modal - View task-->

                                            <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item text-muted"><?php echo __('no_tasks_in_this_group_yet'); ?>.</li>
                                            <?php endif; ?>
                                        </ul>

                                  
                                </div>

                        <!--begin::Modals-->
                            <!--begin::Modal - Edit task group-->
                            <div class="modal fade" id="kt_modal_edit_task_group<?php echo $group['id'];?>" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-900px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header">
                                            <!--begin::Modal title-->
                                            <h2><?php echo __('edit_task_group'); ?></h2>
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

                                                <form method="POST" action="/index.php?controller=TaskGroup&action=<?php echo $group['id'] ? 'update' : 'store'; ?>">
                                                    <?php if ($group['id']) : ?>
                                                        <input type="hidden" name="id" value="<?php echo $group['id']; ?>">
                                                    <?php endif; ?>
                                                    <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label"><?php echo __('title'); ?></label>
                                                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($group['title']); ?>">
                                                    </div>

                                                  
                                                        <input type="hidden" name="position" class="form-control" value="<?php echo htmlspecialchars($group['position']); ?>">
                                                  

                                                    <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                                                   
                                                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>

                                                </form>

                                            </div>
                                        </div>
                                        <!--end::Modal body-->
                                    </div>
                                    <!--end::Modal content-->
                                </div>
                                <!--end::Modal dialog-->
                            </div>
                            <!--end::Modal - Edit task group-->


                            <!--begin::Modals-->
                            <!--begin::Modal - Create new task -->
                            <div class="modal fade" id="kt_modal_add_task<?php echo $group['id'];?>" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-900px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header">
                                            <!--begin::Modal title-->
                                            <h2><?php echo __('create_task_group'); ?></h2>
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
                                        <form method="POST" action="/index.php?controller=Task&action=store">
                                                <input type="hidden" name="task_group_id" value="<?php echo $group['id']; ?>">

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('task_text'); ?></label>
                                                        <textarea name="task_text" class="form-control" required rows="4"></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('scenario'); ?></label>
                                                        <textarea name="scenario" class="form-control" rows="4"></textarea>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('script_what_moderator_should_say'); ?></label>
                                                        <textarea name="script" class="form-control" rows="3"></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('metrics_what_to_observe'); ?></label>
                                                        <textarea name="metrics" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('type_of_evaluation'); ?></label>
                                                        <select name="task_type" id="task_type" class="form-select">
                                                            <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                                                                <option value="<?php echo $type; ?>">
                                                                    <?php echo ucfirst($type); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label"><?php echo __('predefined_evaluation_type_optional'); ?></label>
                                                        <select class="form-select" id="preset-options">
                                                            <option value="">— Select a common type —</option>
                                                            <option value="Completed:completed;Incompleted:incomplete">Completed/Incompleted</option>
                                                            <option value="Yes:yes;No:no">Yes / No</option>
                                                            <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1-5)</option>
                                                            <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1-5)</option>
                                                            <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1-5)</option>
                                                        </select>
                                                        <small class="form-text text-muted">Choosing one will auto-fill the options below.</small>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo __('evaluation_options'); ?></label>
                                                    <textarea name="task_options" id="task_options" class="form-control" rows="3"></textarea>
                                                    <small class="form-text text-muted">Use the format: <code>Label:Value;Label:Value;</code></small>
                                                </div>

                                            
                                                    <input type="hidden" name="position" class="form-control" value="0">
                                            
                                                <div class="d-flex gap-2 mt-4">
                                                    <input type="hidden" name="preset_type" id="preset_type" value="">
                                                    <button type="submit" class="btn btn-primary"><?php echo __('save_task_group'); ?></button>
                                                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>
                                                </div>
                                            </form>
                                        </div>
                                        <!--end::Modal body-->
                                    </div>
                                    <!--end::Modal content-->
                                </div>
                                <!--end::Modal dialog-->
                            </div>
                            <!--end::Modal - Create new task-->
                            <!--end::Modals-->

                                <?php endforeach; ?>
                            <div class="my-3 text-end">
                                <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_create_task_group">+ Add Task Group</a>
                            </div>
                      
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center py-3"><?php echo __('no_task_groups_yet'); ?>. <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_create_task_group">+ Add Task Group</a>
                            </div>
                        <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="questionnairegroup" role="tabpanel">
                            <!-- Questionnaire Groups -->
                            <!-- Task Groups -->
                            <?php if (!empty($questionnaireGroups)) : ?>
                            
                            <div id="questions-group-list">
                                <?php foreach ($questionnaireGroups as $qGroup): ?>
                                     <div class="card mb-4 shadow-sm task-group" id="questionnairegroup<?php echo $qgroup['id']; ?>" data-id="<?php echo $qGroup['id']; ?>">
                                        <div class="card-header d-flex justify-content-between align-items-center p-4">
                                            <div class="d-flex align-items-center">
                                                <div style="cursor: grab;"> 
                                                    <i class="ki-duotone ki-abstract-14 fs-2x">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                                <div class="mx-4">
                                                    <h3 class="text-start"><?php echo htmlspecialchars($qGroup['title']); ?></h3>
                                                </div>
                                            </div>   
                                            <div class="d-flex text-end">
                                            <a href="/index.php?controller=Question&action=generateSUS&group_id=<?php echo $qGroup['id']; ?>&test_id=<?php echo $test['id']; ?>" 
                                                class="btn btn-warning me-2"
                                                onclick="return confirm('Generate SUS questions in this group? This will add 10 questions.')">
                                                    🧠 <?php echo __('add_sus_questions'); ?>
                                                </a> 
                                                <a href="/index.php?controller=Question&action=create&group_id=<?php echo $qGroup['id']; ?>" class="btn btn-primary me-2"  data-bs-toggle="modal" data-bs-target="#kt_modal_add_question<?php echo $qGroup['id']; ?>">+ <?php echo __('add_new_question'); ?></a>
                                                
                                                
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
                                                        <a href="/index.php?controller=Response&action=exportCsvByQuestionnaireGroup&group_id=<?php echo $qGroup['id']; ?>" class="menu-link bg-outline-warning px-3">📥 <?php echo __('export_answers'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=QuestionnaireGroup&action=edit&id=<?php echo $qGroup['id']; ?>" class="menu-link bg-outline-info px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_questionnaire_group<?php echo $qGroup['id']; ?>"><?php echo __('edit_questionnaire'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=QuestionnaireGroup&action=duplicate&id=<?php echo $qGroup['id']; ?>" class="menu-link bg-outline-info text-start px-3"><?php echo __('duplicate_questionnaire'); ?></a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="/index.php?controller=QuestionnaireGroup&action=destroy&id=<?php echo $qGroup['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('are_you_sure_you_want_to_delete_this_questionnaire'); ?>');"><?php echo __('delete_questionnaire'); ?></a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->                       
                                            </div>
                                        </div>

                                        <ul class="list-group list-group-flush task-list" data-group-id="<?php echo $qGroup['id']; ?>">
                                            <?php if (!empty($qGroup['questions'])) : ?>
                                                <?php foreach ($qGroup['questions'] as $question): ?>
                                                    <li class="list-group-item d-flex justify-content-between task-item draggable" data-id="<?php echo $question['id']; ?>">
                                                        <div class="d-flex align-items-center">
                                                            <div style="cursor: grab;"> 
                                                                <i class="ki-duotone ki-abstract-14 fs-2x">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </div>
                                                            <div class="mx-4">
                                                                <h4 class="text-start"><?php echo htmlspecialchars($question['text']); ?></h4>
                                                            </div>
                                                        </div>   
                                                       
                                                        <div class="text-end">
                                                            <a href="#" class="btn btn-sm btn-secondary"  data-bs-toggle="modal" data-bs-target="#kt_modal_view_question<?php echo $question['id']; ?>"><?php echo __('view_question'); ?></a>
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
                                                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase text-start"><?php echo __('settings'); ?></div>
                                                                </div>
                                                                <!--end::Heading-->
                                                                <div class="separator my-2"></div>
                                                                <!--end:Menu item-->
                                                                <!--begin::Menu item-->
                                                                
                                                                
                                                            
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Question&action=edit&id=<?php echo $question['id']; ?>" class="menu-link bg-outline-info px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_question<?php echo $question['id']; ?>"><?php echo __('edit_question'); ?></a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Question&action=duplicate&id=<?php echo $question['id']; ?>" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_question'); ?></a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="/index.php?controller=Question&action=destroy&id=<?php echo $question['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('Are you sure you want to delete this questionnaire group'); ?>"><?php echo __('delete_question'); ?></a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                            
                                                            </div>
                                                            <!--end::Menu 3-->                       

                                                        </div>
                                                    </li>
                                                     <!--begin::Modal - Edit question -->
                                                        <div class="modal fade" id="kt_modal_edit_question<?php echo $question['id']; ?>" tabindex="-1" aria-hidden="true">
                                                            <!--begin::Modal dialog-->
                                                            <div class="modal-dialog modal-dialog-centered mw-900px">
                                                                <!--begin::Modal content-->
                                                                <div class="modal-content">
                                                                    <!--begin::Modal header-->
                                                                    <div class="modal-header">
                                                                        <!--begin::Modal title-->
                                                                        <h2><?php echo __('edit_question'); ?></h2>
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
        <form method="POST" action="/index.php?controller=Question&action=update">
        <?php if ($question['id']) : ?>
            <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
        <?php endif; ?>

        <input type="hidden" name="questionnaire_group_id" value="<?php echo $qGroup['id']; ?>">
        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
        <input type="hidden" name="preset_type" id="preset_type" value="<?php echo htmlspecialchars($question['preset_type'] ?? ''); ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label"><?php echo __('question_text'); ?></label>
                <textarea name="text" class="form-control" required rows="4"><?php echo htmlspecialchars($question['text']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo __('type_of_response') ?></label>
                <select name="question_type" id="question_type" class="form-select">
                    <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $question['question_type'] === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label mt-4"><?php echo __('predefined_evaluation_type'); ?> (<?php echo __('optional'); ?>)</label>
                <select class="form-select" id="preset-options">
                    <option value="">— Select a common type —</option>
                    <option value="Completed:completed;Incompleted:incomplete">Completed/Incompleted</option>
                    <option value="Yes:yes;No:no">Yes / No</option>
                    <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1-5)</option>
                    <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1-5)</option>
                    <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1-5)</option>
                </select>
                <small class="form-text text-muted">This will auto-fill the response type and options below.</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label"><?php echo __('response_options'); ?></label>
            <textarea name="question_options" id="question_options" class="form-control" rows="3"><?php echo htmlspecialchars($question['question_options']); ?></textarea>
            <small class="form-text text-muted">Use <code>Label:Value;Label:Value</code> format for choice-based questions.</small>
        </div>

            <input type="hidden" name="position" class="form-control" value="<?php echo $question['position']; ?>">


        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><?php echo __('save_question'); ?></button>
            <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>
        </div>
    </form>




                                                                </div>
                                                                <!--end::Modal body-->
                                                            </div>
                                                            <!--end::Modal content-->
                                                        </div>
                                                        <!--end::Modal dialog-->
                                                    </div>
                                                    <!--end::Modal - Create new task-->


                                                    <!--begin::Modal - view task -->
                                                    <div class="modal fade" id="kt_modal_view_question<?php echo $question['id']; ?>" tabindex="-1" aria-hidden="true">
                                                        <!--begin::Modal dialog-->
                                                        <div class="modal-dialog modal-dialog-centered mw-900px">
                                                            <!--begin::Modal content-->
                                                            <div class="modal-content">
                                                                <!--begin::Modal header-->
                                                                <div class="modal-header">
                                                                    <!--begin::Modal title-->
                                                                    <h2><?php echo __('view_question'); ?></h2>
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
                                                                <div class="modal-body py-lg-10 px-lg-10 fs-3">
    




                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12 border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('question_text'); ?></p>
                                                                            <p><?php echo htmlspecialchars($question['text']); ?></p>
                                                                        </div>
                                                                        <div class="col-md-12  border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('question_type'); ?></p>
                                                                            <p><?php echo $question['question_type']; ?></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12  border border-1 border-gray-300 rounded p-3">
                                                                            <p class="fw-bold"><?php echo __('type_of_response'); ?></p>
                                                                            <p><?php echo $question['question_type']; ?></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12 border border-1 border-gray-300 rounded p-3 ">
                                                                            <p class="fw-bold"><?php echo __('response_options'); ?></p>
                                                                            <p><?php echo htmlspecialchars($question['question_options']); ?></p>
                                                                        </div>
                                                                    </div>

                                                                
                                                            </div>
                                                            <!--end::Modal body-->
                                                        </div>
                                                        <!--end::Modal content-->
                                                    </div>
                                                    <!--end::Modal dialog-->
                                                </div>
                                                <!--end::Modal - View task-->

                                            <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item text-muted"><?php echo __('no_tasks_in_this_group_yet'); ?>.</li>
                                            <?php endif; ?>
                                        </ul>

                                  
                                </div>

                        <!--begin::Modals-->
                            <!--begin::Modal - Edit task group-->
                            <div class="modal fade" id="kt_modal_edit_questionnaire_group<?php echo $qGroup['id'];?>" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-900px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header">
                                            <!--begin::Modal title-->
                                            <h2><?php echo __('edit_questionnaire_group'); ?></h2>
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
                                            <?php echo $qGroup['id']; ?>
                                                <form method="POST" action="/index.php?controller=TaskGroup&action=<?php echo $qGroup['id'] ? 'update' : 'store'; ?>">
                                                    <?php if ($qGroup['id']) : ?>
                                                        <input type="hidden" name="id" value="<?php echo $qGroup['id']; ?>">
                                                    <?php endif; ?>
                                                    <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label"><?php echo __('title'); ?></label>
                                                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($qGroup['title']); ?>">
                                                    </div>

                                                  
                                                        <input type="hidden" name="position" class="form-control" value="<?php echo htmlspecialchars($qGroup['position']); ?>">
                                                  

                                                    <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                                                   
                                                    <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>

                                                </form>

                                            </div>
                                        </div>
                                        <!--end::Modal body-->
                                    </div>
                                    <!--end::Modal content-->
                                </div>
                                <!--end::Modal dialog-->
                            </div>
                            <!--end::Modal - Edit task group-->


                            <!--begin::Modals-->
                            <!--begin::Modal - Create new question -->
                            <div class="modal fade" id="kt_modal_add_question<?php echo $qGroup['id'];?>" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-900px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header">
                                            <!--begin::Modal title-->
                                            <h2><?php echo __('add_question'); ?></h2>
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
                                        <form method="POST" action="/index.php?controller=Question&action=store">
                                          <input type="hidden" name="questionnaire_group_id" value="<?php echo $qGroup['id']; ?>">
        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
        <input type="hidden" name="preset_type" id="preset_type" value="">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label"><?php echo __('question_text') ?></label>
                <textarea name="text" class="form-control" required rows="4"><?php echo htmlspecialchars($question['text']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo __('type_of_response') ?></label>
                <select name="question_type" id="question_type" class="form-select">
                    <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $question['question_type'] === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label mt-4"><?php echo __('predefined_evaluation_type'); ?> (<?php echo __('optional'); ?>)</label>
                <select class="form-select" id="preset-options">
                    <option value="">— Select a common type —</option>
                    <option value="Completed:completed;Incompleted:incomplete">Completed/Incompleted</option>
                    <option value="Yes:yes;No:no">Yes / No</option>
                    <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1-5)</option>
                    <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1-5)</option>
                    <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1-5)</option>
                </select>
                <small class="form-text text-muted"><?php echo __('this_will_auto_fill_the_response_type_and_options_below'); ?>.</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label"><?php echo __('response_options'); ?></label>
            <textarea name="question_options" id="question_options" class="form-control" rows="3"><?php echo htmlspecialchars($question['question_options']); ?></textarea>
            <small class="form-text text-muted">Use <code>Label:Value;Label:Value</code> format for choice-based questions.</small>
        </div>

        <input type="hidden" name="position" class="form-control" value="<?php echo $question['position']; ?>">

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><?php echo __('save_question'); ?></button>
            <?php
$anchor = $question['id'] ? '#questionnaire-group' . $question['questionnaire_group_id'] : '#questionnaire-group-list';
$cancelUrl = '/index.php?controller=Test&action=show&id=' . $context['test_id'] . $anchor;
?>
<a href="<?php echo $cancelUrl; ?>" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
        </div>
    </form>

                                        </div>
                                        <!--end::Modal body-->
                                    </div>
                                    <!--end::Modal content-->
                                </div>
                                <!--end::Modal dialog-->
                            </div>
                            <!--end::Modal - Create new task-->
                            <!--end::Modals-->

                                <?php endforeach; ?>
                            <div class="my-3 text-end">
                                
                                <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_create_questionnaire_group">+ <?php echo __('add_questionnaire_group'); ?></a>
                            </div>
                      
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center py-3">No Questionnaire Groups yet. <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_create_questionnaire_group">+ Add Questionnaire Group</a>
                            </div>
                        <?php endif; ?>

                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <!--begin::Modal - Create Task Group-->
        <div class="modal fade" id="kt_modal_create_task_group" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2><?php echo __('create_a_new_task_group'); ?></h2>
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
                        
                        <form method="POST" action="/index.php?controller=TaskGroup&action=store">
                            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

                            <div class="mb-3">
                                <label class="form-label"><?php echo __('title'); ?></label>
                                <input type="text" name="title" class="form-control" required value="">
                            </div>

                                <input type="hidden" name="position" class="form-control" value="0">
                     

                            <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                            
                            <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>

                        </form>
                    
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - Create task group -->


        <!--begin::Modal - Create Questionnaire Group-->
        <div class="modal fade" id="kt_modal_create_questionnaire_group" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2><?php echo __('create_a_new_questionnaire_group'); ?></h2>
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
                    <form method="POST" action="/index.php?controller=QuestionnaireGroup&action=store">
                        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label"><?php echo __('title'); ?></label>
                            <input type="text" name="title" class="form-control" required value="">
                        </div>

                        <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                        <a href="#" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></a>

                    </form>
                    
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - Create task group -->



<!--begin::Toast Container-->
<div id="kt_toast_container" class="toast-container position-fixed bottom-0 end-0 p-3">
    <!-- Placeholder toast template -->
    <div class="toast align-items-center text-bg-secondary border-0 d-none" role="alert" aria-live="assertive" aria-atomic="true" data-template="true">
        <div class="toast-header fs-4 text-bg-secondary">
            <i class="ki-duotone ki-check-circle fs-2 text-white me-3"><span class="path1"></span><span class="path2"></span></i>
            <strong class="me-auto text-white">Success</strong>
            <small class="text-white">Just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body fs-4 text-white">
            Toast message
        </div>
    </div>
</div>
<!--end::Toast Container-->

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;

        // Se o hash começar por #taskgroup, ativa a tab de Task Groups
        if (hash.startsWith("#taskgroup")) {
            const taskTab = document.querySelector('a[data-bs-toggle="tab"][href="#taskgroup"]');
            if (taskTab) {
                const tab = new bootstrap.Tab(taskTab);
                tab.show();

                // Faz scroll suave até ao grupo
                const target = document.querySelector(hash);
                if (target) {$group['id']
                    setTimeout(() => {
                        target.scrollIntoView({ behavior: "smooth", block: "start" });
                    }, 300); // aguarda a transição da tab
                }
            }
        }
        if (hash.startsWith("#questionnairegroup")) {
            const questionTab = document.querySelector('a[data-bs-toggle="tab"][href="#questionnairegroup"]');
            if (questionTab) {
                const tab = new bootstrap.Tab(questionTab);
                tab.show();

                // Faz scroll suave até ao grupo
                const target = document.querySelector(hash);
                if (target) {$qGroup['id']
                    setTimeout(() => {
                        target.scrollIntoView({ behavior: "smooth", block: "start" });
                    }, 300); // aguarda a transição da tab
                }
            }
        }
    });

    <?php if (!empty($_SESSION['toast_success'])) : ?>

window.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('kt_toast_container');
    const template = container.querySelector('[data-template]');
    const toastClone = template.cloneNode(true);

    toastClone.classList.remove('d-none');
    toastClone.removeAttribute('data-template');
    toastClone.querySelector('.toast-body').innerText = <?php echo json_encode($_SESSION['toast_success']); ?>;

    container.appendChild(toastClone);
    const toast = bootstrap.Toast.getOrCreateInstance(toastClone);
    toast.show();
});

        <?php unset($_SESSION['toast_success']); ?>
    <?php endif; ?>

    const presets = {
    "Completed/Incompleted": {
        type: "radio",
        options: "Completed:completed;Incompleted:incomplete"
    },
    "Yes / No": {
        type: "radio",
        options: "Yes:yes;No:no"
    },
    "Agreement Scale (1-5)": {
        type: "radio",
        options: "Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5"
    },
    "Difficulty Scale (1-5)": {
        type: "radio",
        options: "Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5"
    },
    "Satisfaction Scale (1-5)": {
        type: "radio",
        options: "Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5"
    }
};

document.getElementById('preset-options').addEventListener('change', function () {
    const selectedLabel = this.options[this.selectedIndex].text;
    const preset = presets[selectedLabel];

    if (preset) {
        document.getElementById('preset_type').value = selectedLabel;
        document.getElementById('task_options').value = preset.options;
        document.getElementById('task_type').value = preset.type;
    }
});
</script>

</body>
</html>
