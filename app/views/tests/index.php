<?php 
$title = __('project_details_tests'); 
$pageTitle = __('project_details_tests');
$pageDescription = __('manage_project_test_sessions');
$menuActive = 'tests';
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

            <!--begin::Toolbar-->
            <div class="d-flex flex-wrap flex-stack pt-10 pb-8">
                <!--begin::Heading-->
                <h3 class="fw-bold my-2"><?php echo __('tests'); ?>
                <span class="fs-6 text-gray-500 fw-semibold ms-1"><?php echo __('by_recent_updates'); ?></span></h3>
                <!--end::Heading-->
                <!--begin::Controls-->
                <div class="d-flex flex-wrap my-1">
                   <a href="/index.php?controller=Test&action=create&project_id=<?php echo $project['id']; ?>" class="btn btn-light bg-white me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_create_test">+ <?php echo __('create_new_test'); ?></a>
                </div>
                <!--end::Controls-->
            </div>
            <!--end::Toolbar-->
                <div id="kt_project_targets_card_pane" class="tab-pane fade show active">
                    <!--begin::Row-->
                    <div class="row g-9">

                    <?php if (!empty($tests)) : ?>
                        <?php foreach ($tests as $test): ?>
                        
                        <!--begin::Col-->
                        <div class="col-md-4 col-lg-12 col-xl-4">    
                            <!--begin::Card-->
                            <div class="card mb-6 mb-xl-9">
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Header-->
                                    <div class="d-flex flex-stack mb-3">
                                        <!--begin::Badge-->
                                        <?php if (!empty($test['status'])) : ?>
                                             <?php if ($test['status'] == 'complete') : ?>
                                                <div class="badge badge-light-success"><?php echo __('completed'); ?></div>
                                            <?php else : ?>
                                                <?php if ($test['session_count'] > 0) : ?>
                                                    <div class="badge badge-light-warning"><?php echo __('in_progress'); ?></div>
                                                <?php elseif ($test['session_count'] == 0) : ?>
                                                    <div class="badge badge-light-danger"><?php echo __('not_yet_started'); ?></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                      
                                        <!--end::Badge-->
                                        <!--begin::Menu-->
                                        <div>
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
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <!--begin::Switch-->
                                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                                        <input 
                                                            class="form-check-input w-30px h-20px toggle-test-status"
                                                            type="checkbox"
                                                            value="1"
                                                            data-test-id="<?php echo $test['id']; ?>"
                                                            <?php if ($test['status'] == 'complete') : ?>checked<?php 
                                                            endif; ?>
                                                        />
                                                        <span class="form-check-label text-muted fs-6"><?php echo __('completed'); ?></span>
                                                    </label>
                                                    <!--end::Switch-->
                                                </div>
                                                <!--end::Menu item-->
                                                <div class="separator my-2"></div>
                                                <!--end:Menu item-->
                                                <!--begin::Menu item-->
                                                
                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Test&action=edit&id=<?php echo $test['id']; ?>" class="menu-link bg-outline-warning px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_test_<?php echo $test['id']; ?>"><?php echo __('edit_test'); ?></a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Test&action=show&id=<?php echo $test['id']; ?>" class="menu-link bg-outline-warning px-3"><?php echo __('manage_tasks_questions'); ?></a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Test&action=duplicate&id=<?php echo $test['id']; ?>" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_test'); ?></a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Test&action=destroy&id=<?php echo $test['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('confirm_delete_test'); ?>');"><?php echo __('delete_test'); ?></a>
                                                </div>
                                                <!--end::Menu item-->
                                            
                                            </div>
                                            <!--end::Menu 3-->
                                        </div>
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Title-->
                                    <div class="mb-2">
                                        <a href="/index.php?controller=Test&action=show&id=<?php echo $test['id']; ?>" class="fs-4 fw-bold mb-1 text-gray-900 text-hover-primary"><?php echo htmlspecialchars($test['title']); ?></a>
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Content-->
                                    <div class="fs-6 fw-semibold text-gray-600 mb-5"><?php echo htmlspecialchars($test['description']); ?></div>
                                    <!--end::Content-->
                                    <!--begin::Footer-->
                                    <div class="d-flex flex-stack flex-wrapr">
                                        <!--begin::Stats-->
                                        <div class="d-flex my-1">
                                          <!--begin::Stat-->
                                          <div class="border border-dashed border-gray-300 d-flex align-items-center rounded py-2 px-3  my-1 " data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo __('tasks_in_test'); ?>">
                                                <i class="bi bi-list-task fs-3"></i>
                                                <span class="ms-1 fs-7 fw-bold text-gray-600" ><?php echo $test['task_count']; ?></span>
                                            </div>
                                        
                                            <!--begin::Stat-->
                                            <div class="border border-dashed border-gray-300 rounded d-flex align-items-center py-2 px-3 my-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo __('questions_in_test'); ?>">
                                                <i class="bi bi-patch-question fs-3"></i>
                                                <span class="ms-1 fs-7 fw-bold text-gray-600"><?php echo $test['question_count']; ?></span>
                                            </div>
                                            <!--end::Stat-->
                                            </div>
                                            <div class="d-flex my-1">
                                            <!--begin::Stat-->
                                            <div class="border border-dashed border-gray-300 rounded d-flex align-items-center py-2 px-3  my-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo __('sessions_done'); ?>">
                                                <i class="bi bi-check2-square fs-3"></i>
                                                <span class="ms-1 fs-7 fw-bold text-gray-600">
                                
                                                <?php echo $test['session_count']; ?></span>
                                            </div>
                                        
                                            <!--end::Stat-->
                                        </div>
                                        <!--end::Stats-->
                                    </div>
                                    <!--end::Footer-->
                                    <div class="d-flex mt-5">
                                            <div class="separator"></div>
                                            <a href="/index.php?controller=Test&action=show&id=<?php echo $test['id']; ?>" class="btn btn-info opacity-50 w-100"><i class="bi bi-gear fs-3"></i> <?php echo __('manage_tasks_questions'); ?></a>
                                        </div>

                                        
                                            <!--end::Stat-->
                                            <div class="separator my-3"></div>
                                        <!--begin::Actions-->
                                        <?php 
                                        if ($test['task_count'] != 0): ?>
                                            <div class="d-flex my-1">
                                                <div class="separator"></div>
                                                <a href="/index.php?controller=Session&action=startTaskSession&test_id=<?php echo $test['id']; ?>" class="btn btn-primary w-100"><?php echo __('start_task_session'); ?></a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($test['question_count'] != 0): ?>
                                        <div class="d-flex my-1">
                                            <div class="separator"></div>
                                            <a href="/index.php?controller=Session&action=startQuestionnaire&test_id=<?php echo $test['id']; ?>" class="btn btn-secondary w-100"><?php echo __('start_questionnaire_session'); ?></a>
                                        </div>
                                        <?php endif; ?>
                                        <!--end::Actions-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                        
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-warning"><?php echo __('no_tests_created_yet'); ?></div>
                    <?php endif; ?>

                    </div>
                    <!--end::Row-->

              
            </div>
            <!--end::Tab Content-->                    
            <!--end::Row-->
        </div>


    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

        <!--begin::Modals-->
        <!--begin::Modal - Create test-->
        <div class="modal fade" id="kt_modal_create_test" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2><?php echo __('create_new_test_title'); ?></h2>
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

                        <form method="POST" enctype="multipart/form-data" action="/index.php?controller=Test&action=store">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <input type="hidden" name="status" value="incomplete">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo __('title'); ?></label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><?php echo __('description'); ?></label>
                                            <textarea name="description" class="form-control" rows="3"></textarea>
                                        </div>

                                    </div>

                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo __('layout_image'); ?></label>
                                            <input type="file" name="layout_image" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary"><?php echo __('create_test'); ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - Create test-->

        <?php foreach ($tests as $test): ?>
    <!-- Modal de edição -->
    <div class="modal fade" id="kt_modal_edit_test_<?php echo $test['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><?php echo __('edit_test'); ?></h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body py-lg-10 px-lg-10">
                    <form method="POST" enctype="multipart/form-data" action="/index.php?controller=Test&action=update">
                        <input type="hidden" name="id" value="<?php echo $test['id']; ?>">
                        <input type="hidden" name="project_id" value="<?php echo $test['project_id']; ?>">
                        <input type="hidden" name="existing_layout_image" value="<?php echo htmlspecialchars($test['layout_image']); ?>">

                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo __('title'); ?></label>
                                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($test['title']); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?php echo __('description'); ?></label>
                                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($test['description']); ?></textarea>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="complete" <?php if ($test['status'] == 'complete') { echo 'checked';
                                                                                                                   } ?>>
                                    <label class="form-check-label"><?php echo __('mark_as_completed'); ?></label>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo __('replace_layout_image'); ?></label>
                                    <input type="file" name="layout_image" class="form-control" accept="image/*">
                                </div>
                                <?php if ($test['layout_image']) : ?>
                                    <div class="mb-3">
                                        <img src="/uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" class="img-fluid" alt="<?php echo __('current_layout'); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><?php echo __('save_changes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <?php endforeach; ?>
        <!--end::Modals-->


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
  const statusToggles = document.querySelectorAll('.toggle-test-status');

  statusToggles.forEach(input => {
    input.addEventListener('change', async (e) => {
      const testId = e.target.dataset.testId;
      const isComplete = e.target.checked;

      const response = await fetch('/index.php?controller=Test&action=toggleStatus', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: testId,
          status: isComplete ? 'complete' : 'incomplete'
        })
      });

      if (!response.ok) {
        alert('<?php echo __('error_update_status'); ?>');
        // Revert checkbox
        e.target.checked = !isComplete;
      }
    });
  });
});
</script>

</body>
</html>
