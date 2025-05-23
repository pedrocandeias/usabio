<?php
$title = 'Project details - Participants';

$pageTitle = 'Participants in ' . $project['project_name'];
$menuActive = 'participants';
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


        <!--begin::Table-->
        <div class="card card-flush mt-6 mt-xl-9">
            <!--begin::Card header-->
            <div class="card-header mt-5">
                <!--begin::Card title-->
                <div class="card-title flex-column">
                    <h3 class="fw-bold mb-1"><?php echo __('participants_in_project'); ?></h3>

                    <?php if (!empty($participants)): ?>
                        <div class="fs-6 text-gray-500"><?php echo count($participants); ?> <?php echo __('added_to_the_project'); ?></div>
                    <?php endif; ?>
                </div>
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar my-1">
                    <!--begin::Select-->
                    <div class="me-6 my-1">
                        <a href="/index.php?controller=Participant&action=create&project_id=<?php echo $project_id; ?>" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_participant<?php echo $project_id; ?>">+ <?php echo __('add_new_participant'); ?></a>

                    </div>
                    <!--end::Select-->
                    <!--begin::Select-->
                    <!-- <div class="me-4 my-1">
                    <select id="kt_filter_orders" name="orders" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-solid form-select-sm">
                        <option value="All" selected="selected">All status</option>
                        <option value="Approved">Done tasks</option>
                        <option value="Approved">Done questionnaires</option>
                        <option class="approved" value="Completed">Done Tasks and Questionnaires</option>
                        <option value="In Transit">Hasn't done Tasks</option>
                        <option value="In Transit">Hasn't done Questionnaires</option>
                    </select>
                </div> -->
                    <!--end::Select-->

                </div>
                <!--begin::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">


                <?php if (!empty($participants)): ?>
                    <div class="table-responsive">

                        <table id="kt_profile_overview_table" class="table table-striped table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                            <thead class="fs-7 text-gray-500 text-uppercase">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th><?php echo __('name'); ?></th>
                                    <th class="text-center"><?php echo __('age'); ?></th>
                                    <th class="text-center"><?php echo __('gender'); ?></th>
                                    <th class="text-center"><?php echo __('academic_qualification'); ?></th>
                                    <th class="text-center"><?php echo __('assigned_to'); ?></th>
                                    <th class="text-center"><?php echo __('sessions'); ?></th>
                                    <th class="text-center"><?php echo __('last_evaluation'); ?></th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                <?php foreach ($participants as $participant): ?>

                                    <tr>
                                        <td><?php echo htmlspecialchars($participant['id']); ?></td>
                                        <td>
                                            <!--begin::User-->
                                            <div class="d-flex align-items-center">

                                                <!--begin::Info-->
                                                <div class="d-flex flex-column justify-content-center">
                                                    <a href="/index.php?controller=Participant&action=edit&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>" class="fs-6 text-gray-800 text-hover-primary"> <?php echo htmlspecialchars($participant['participant_name']); ?></a>

                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::User-->

                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($participant['participant_age'] ?? 'Anonymous'); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($participant['participant_gender'] ?? 'Anonymous'); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($participant['participant_academic_level'] ?? 'Anonymous'); ?></td>
                                        <td class="text-center"><?php


                                                                $tests_by_participant = $testsByParticipant[$participant['id']] ?? [];

                                                                $tests_title = array_map(function ($test) {
                                                                    return $test['test_title'];
                                                                }, $tests_by_participant);

                                                                if (!empty($tests_title)) { ?>
                                                <?php foreach ($tests_title as $title): ?>
                                                    <span class="badge text-bg-info d-block mb-2"><?php echo htmlspecialchars($title) ?></span>
                                                <?php endforeach; ?>

                                            <?php } else {
                                                                    echo '<span class="text-muted">' . __('No tests assigned') . '</span>';
                                                                }
                                            ?>
                                        </td>
                                        <td> <?php
                                                $completed = $completedTestsByParticipant[$participant['id']] ?? [];
                                                if (!empty($completed)) {
                                                    foreach ($completed as $title) {
                                                        echo '<span class="badge bg-success me-1">' . htmlspecialchars($title) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted">None</span>';
                                                }
                                                ?>
                                        </td>

                                        <!-- <td class="text-center"><?php echo isset($participant['session_count'])
                                                                            ? htmlspecialchars($participant['session_count'])
                                                                            : '<span  class="badge badge-warning text-white fst-italic">N/A</span>'; ?></td> -->
                                        <td class="text-center">


                                            <?php echo isset($participant['last_evaluation'])
                                                ? htmlspecialchars($participant['last_evaluation'])
                                                : '<span  class="badge badge-warning text-white fst-italic">N/A</span>'; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-light btn-sm" href="/index.php?controller=Participant&action=show&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>" data-bs-toggle="modal" data-bs-target="#kt_modal_view_participant<?php echo $participant['id']; ?>"><?php echo __('view_full_profile'); ?></a>
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
                                                <!--end:Menu item-->ª
                                                <!--begin::Menu item-->


                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Participant&action=edit&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>" class="menu-link bg-outline-info px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_participant<?php echo $participant['id']; ?>"><?php echo __('edit_participant'); ?></a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="/index.php?controller=Participant&action=destroy&id=<?php echo $participant['id']; ?>&project_id=<?php echo $project_id; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('Are you sure you want to delete this participant?');"><?php echo __('delete_participant'); ?></a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu 3-->


                                        </td>
                                    </tr>
                                    <!--begin::Modal - Edit participant -->
                                    <div class="modal fade" id="kt_modal_edit_participant<?php echo $participant['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog modal-dialog-centered mw-900px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header">
                                                    <!--begin::Modal title-->
                                                    <h2><?php echo __('edit_participant'); ?></h2>
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
                                                    <?php $participant_id = $participant['id']; ?>
                                                    <form method="POST" action="/index.php?controller=Participant&action=update">
                                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                                        <?php if (!empty($participant_id)): ?>
                                                            <input type="hidden" name="participant_id" value="<?php echo $participant_id; ?>">
                                                        <?php endif; ?>
                                                        <?php if (!empty($project_id)): ?>
                                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label"><?php echo __('participant_name'); ?></label>
                                                            <input type="text" name="participant_name" class="form-control" value="<?php echo htmlspecialchars($participant['participant_name'] ?? ''); ?>" required>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="form-label"><?php echo __('age'); ?></label>
                                                                <input type="number" name="participant_age" class="form-control" value="<?php echo htmlspecialchars($participant['participant_age'] ?? ''); ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label"><?php echo __('gender'); ?></label>
                                                                <select name="participant_gender" class="form-select">
                                                                    <option value=""><?php echo __('select'); ?></option>
                                                                    <option value="female" <?php echo ($participant['participant_gender'] ?? '') === 'female' ? 'selected' : ''; ?>><?php echo __('female0'); ?></option>
                                                                    <option value="male" <?php echo ($participant['participant_gender'] ?? '') === 'male' ? 'selected' : ''; ?>><?php echo __('male'); ?></option>
                                                                    <option value="nonbinary" <?php echo ($participant['participant_gender'] ?? '') === 'nonbinary' ? 'selected' : ''; ?>><?php echo __('non_binary'); ?></option>
                                                                    <option value="prefer_not_say" <?php echo ($participant['participant_gender'] ?? '') === 'prefer_not_say' ? 'selected' : ''; ?>><?php echo __('prefer_not_to_say'); ?></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label"><?php echo __('academic_qualification'); ?></label>
                                                                <select name="participant_academic_level" class="form-select">
                                                                    <option value=""><?php echo __('select...'); ?></option>
                                                                    <?php
                                                                    $levels = [
                                                                        'Primary education',
                                                                        'Secondary education',
                                                                        'High school diploma',
                                                                        'Bachelors degree',
                                                                        'Masters degree',
                                                                        'Doctorate / PhD',
                                                                        'Other'
                                                                    ];
                                                                    $selected = $participant['participant_academic_level'] ?? '';
                                                                    foreach ($levels as $level): ?>
                                                                        <option value="<?php echo htmlspecialchars($level); ?>" <?php echo ($selected === $level) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($level); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <?php


                                                        if (!empty($customFields)) { ?>
                                                            <hr class="my-4">
                                                            <h5><?php echo __('custom_fields'); ?></h5>
                                                            <?php foreach ($customFields as $field): ?>
                                                                <div class="mb-3">
                                                                    <label class="form-label"><?php echo htmlspecialchars($field['label']); ?></label>
                                                                    <?php
                                                                    $value = $participant['custom_fields'][$field['id']] ?? '-';
                                                                    if ($field['field_type'] === 'select') {
                                                                        echo '<select name="custom_field[' . $field['id'] . ']" class="form-select">';
                                                                        echo '<option value="">Select...</option>';
                                                                        foreach (explode(';', $field['options']) as $opt) {
                                                                            $opt = trim($opt);
                                                                            $selected = $value === $opt ? 'selected' : '';
                                                                            echo "<option value=\"$opt\" $selected>$opt</option>";
                                                                        }
                                                                        echo '</select>';
                                                                    } elseif ($field['field_type'] === 'number') {
                                                                        echo '<input type="number" name="custom_field[' . $field['id'] . ']" class="form-control" value="' . htmlspecialchars($value) . '">';
                                                                    } else {
                                                                        echo '<input type="text" name="custom_field[' . $field['id'] . ']" class="form-control" value="' . htmlspecialchars($value) . '">';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            <?php endforeach; ?>

                                                        <?php } else { ?>
                                                            <div class="alert alert-warning mt-3 mb-3" role="alert">
                                                                ⚠️ <?php echo __('no_custom_fields_for_participants_found_for_this_project'); ?>. <a href="#" data-bs-dismiss="modal">Create custom fields</a> to collect additional information about participants.
                                                            </div>
                                                        <?php } ?>

                                                        <div class="col-md-12">
                                                            <?php
                                                            $tests_by_participant = $testsByParticipant[$participant['id']] ?? [];
                                                            if (!empty($tests_by_participant)) {
                                                                $assignedTestIds = array_map(function ($test) {
                                                                    return $test['participant_test_id'];
                                                                }, $tests_by_participant);
                                                            }
                                                            if (!empty($tests)): ?>

                                                                <hr class="my-4">
                                                                <h5><?php echo __('tests'); ?></h5>
                                                                <p class="text-muted"><?php echo __('select_the_tests_that_this_participant_will_be_assigned_to'); ?>.</p>
                                                                <div class="mb-5">

                                                                    <div class="form-check">
                                                                        <?php foreach ($tests as $test): ?>
                                                                            <div class="mb-2">
                                                                                <input type="checkbox" name="test_ids[]" value="<?php echo $test['id'] ?>"
                                                                                    class="form-check-input"
                                                                                    id="test_<?php echo $test['id'] ?>"
                                                                                    <?php echo in_array($test['id'], $assignedTestIds ?? []) ? 'checked' : '' ?>>
                                                                                <label class="form-check-label" for="test_<?php echo $test['id'] ?>">
                                                                                    <?php echo htmlspecialchars($test['title']) ?>
                                                                                </label>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php else : ?>
                                                                <div class="alert alert-warning mt-3 mb-3" role="alert">
                                                                    ⚠️<?php echo __('no_tests_found_for_this_project'); ?>. <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#test-list">Create tests</a> to assign to participants.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="mt-2">
                                                            <button type="submit" class="btn btn-primary"><?php echo __('save_participant'); ?></button>
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
                                    <!--end::Modal - Edit new participant-->



                                    <!--begin::Modal - View participant -->
                                    <div class="modal fade" id="kt_modal_view_participant<?php echo $participant['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog modal-dialog-centered mw-900px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header">
                                                    <!--begin::Modal title-->
                                                    <h2><?php echo __('view_participant'); ?></h2>
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
                                                    <?php $participant_id = $participant['id']; ?>

                                                    <!--begin::Row-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted"><?php echo __('participant_name'); ?></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800"><?php echo htmlspecialchars($participant['participant_name'] ?? ''); ?></span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->

                                                    <!--begin::Row-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted"><?php echo __('age'); ?></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800"><?php echo htmlspecialchars($participant['participant_age'] ?? ''); ?></span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->

                                                    <!--begin::Row-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted"><?php echo __('gender'); ?></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800"><?php echo ($participant['participant_gender'] ?? ''); ?></span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                    <!--begin::Row-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted"><?php echo __('academic_qualification'); ?></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800"><?php echo $participant['participant_academic_level'] ?? ''; ?></span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->

                                                    <?php


                                                    if (!empty($customFields)) { ?>
                                                        <hr class="my-4">
                                                        <h5><?php echo __('custom_fields'); ?></h5>
                                                        <?php foreach ($customFields as $field): ?>
                                                            <!--begin::Row-->
                                                            <div class="row mb-7">
                                                                <!-- Label -->
                                                                <label class="col-lg-4 fw-semibold text-muted"><?php echo htmlspecialchars($field['label']); ?></label>

                                                                <!-- Value -->
                                                                <div class="col-lg-8">
                                                                    <span class="fw-bold fs-6 text-gray-800">
                                                                        <?php
                                                                        // Get the value for this field (by field ID)
                                                                        $value = $participant['custom_fields'][$field['id']] ?? '-';
                                                                        echo htmlspecialchars($value);
                                                                        ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>

                                                    <?php } else { ?>
                                                        <div class="alert alert-warning mt-3 mb-3" role="alert">
                                                            ⚠️ <?php echo __('no_custom_fields_for_participants_found_for_this_project'); ?>. <a href="#" data-bs-dismiss="modal"><?php echo __('create_custom_fields'); ?></a> <?php echo __('to_collect_additional_information_about_participants'); ?>.
                                                        </div>
                                                    <?php } ?>

                                                    <div class="col-md-12">
                                                        <?php
                                                        $tests_by_participant = $testsByParticipant[$participant['id']] ?? [];
                                                        if (!empty($tests_by_participant)) {
                                                            $assignedTestIds = array_map(function ($test) {
                                                                return $test['participant_test_id'];
                                                            }, $tests_by_participant);
                                                        }
                                                        if (!empty($tests)): ?>

                                                            <hr class="my-4">
                                                            <h5><?php echo __('tests'); ?></h5>
                                                            <p class="text-muted"><?php echo __('tests_that_this_participant_is_assigned_to'); ?>.</p>

                                                            <div class="mb-5">

                                                                <?php foreach ($tests as $test): ?>
                                                                    <div class="row mb-7">
                                                                        <!--begin::Col-->
                                                                        <div class="col-lg-8">
                                                                            <span class="fw-bold fs-6 text-gray-800"><?php echo htmlspecialchars($test['title']) ?></span>
                                                                        </div>
                                                                        <!--end::Col-->
                                                                    </div>

                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="alert alert-warning mt-3 mb-3" role="alert">
                                                                ⚠️ <?php echo __('no_tests_found_for_this_project'); ?>. <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#test-list">Create tests</a> to assign to participants.
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="mt-2">
                                                        <button type="submit" class="btn btn-primary"><?php echo __('save_participant'); ?></button>
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
                                    <!--end::Modal - Edit new participant-->



                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted"><?php echo __('no_participants_found_for_this_project'); ?>. <a href="/index.php?controller=Participant&action=create&project_id=<?php echo $project_id; ?>" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_participant<?php echo $project_id; ?>"><?php echo __('add_a_new_participant'); ?></a></p>
                <?php endif; ?>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->


        <div class="card mt-5">
            <div class="card-header">
                <h3 class="card-title"><?php echo __('custom_fields_for_participants'); ?></h3>

            </div>
            <div class="card-body">
                <!-- Custom Participant Fields -->
                <div id="custom-fields-list">

                    <form method="POST" action="/index.php?controller=CustomField&action=store" class="row g-3 mb-4">
                        <input type="hidden" name="project_id" value=" <?php echo $project_id; ?>">

                        <div class="col-md-4">
                            <input type="text" name="label" class="form-control" placeholder="Field Label" required>
                        </div>

                        <div class="col-md-4">
                            <select name="field_type" class="form-select" required>
                                <option value="text"><?php echo __('text'); ?></option>
                                <option value="number"><?php echo __('number'); ?></option>
                                <option value="select"><?php echo __('dropdown (select)'); ?></option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="options" class="form-control" placeholder="Options (for select, e.g. A;B;C)">
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success w-100"><?php echo __('add'); ?></button>
                        </div>
                    </form>

                    <?php if (!empty($customFields)) : ?>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo __('label'); ?></th>
                                    <th><?php echo __('type'); ?></th>
                                    <th><?php echo __('options'); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customFields as $field): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($field['label']); ?></td>
                                        <td><?php echo $field['field_type']; ?></td>
                                        <td><?php echo htmlspecialchars($field['options']); ?></td>
                                        <td class="text-end">
                                            <a href="/index.php?controller=ParticipantCustomField&action=edit&id=<?php echo $field['id'] ?>&project_id=<?php echo $project_id; ?>" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_custom_field<?php echo $field['id']; ?>"><?php echo __('edit'); ?></a>
                                            <a href="/index.php?controller=ParticipantCustomField&action=destroy&id=<?php echo $field['id'] ?>&project_id=<?php echo $project_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('are_you_sure_you_want_to_delete_this_field?'); ?>')"><?php echo __('delete'); ?></a>
                                        </td>
                                    </tr>


                                    <!--begin::Modal - Add Custom Field -->
                                    <div class="modal fade" id="kt_modal_edit_custom_field<?php echo $field['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog modal-dialog-centered mw-900px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header">
                                                    <!--begin::Modal title-->
                                                    <h2><?php echo __('edit_custom_field'); ?></h2>
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

                                                    <form method="POST" action="/index.php?controller=ParticipantCustomField&action=<?= $field ? 'update' : 'store' ?>">
                                                        <?php if ($field): ?>
                                                            <input type="hidden" name="id" value="<?= $field['id'] ?>">
                                                        <?php endif; ?>
                                                        <input type="hidden" name="project_id" value="<?= $_GET['project_id'] ?? $field['project_id'] ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Label</label>
                                                            <input type="text" name="label" class="form-control" required value="<?= htmlspecialchars($field['label'] ?? '') ?>">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Field Type</label>
                                                            <select name="field_type" class="form-select">
                                                                <option value="text" <?= ($field['field_type'] ?? '') === 'text' ? 'selected' : '' ?>>Text</option>
                                                                <option value="number" <?= ($field['field_type'] ?? '') === 'number' ? 'selected' : '' ?>>Number</option>
                                                                <option value="select" <?= ($field['field_type'] ?? '') === 'select' ? 'selected' : '' ?>>Dropdown</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Options (for dropdown; use `;` to separate)</label>
                                                            <input type="text" name="options" class="form-control" value="<?= htmlspecialchars($field['options'] ?? '') ?>">
                                                        </div>


                                                        <input type="hidden" name="position" class="form-control" value="<?= htmlspecialchars($field['position'] ?? 0) ?>">


                                                        <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                                                        <a href="#" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
                                                    </form>

                                                </div>
                                                <!--end::Modal body-->
                                            </div>
                                            <!--end::Modal content-->
                                        </div>
                                        <!--end::Modal dialog-->
                                    </div>
                                    <!--end::Modal - Add Custom Field -->


                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            ⚠️ <?php echo __('no_custom_fields_for_participants_found_for_this_project'); ?>
                        </div>
                    <?php endif; ?>
                </div>


            </div>
        </div>


        <!--begin::Modal - Create new participant -->
        <div class="modal fade" id="kt_modal_add_participant<?php echo $project_id; ?>" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2><?php echo __('add_participant'); ?></h2>
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

                        <form method="POST" action="/index.php?controller=Participant&action=store">

                            <?php

                            if (!empty($project_id)): ?>
                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label"><?php echo __('participant_name'); ?></label>
                                <input type="text" name="participant_name" class="form-control" value="" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label"><?php echo __('age'); ?></label>
                                    <input type="number" name="participant_age" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><?php echo __('Gender'); ?></label>
                                    <select name="participant_gender" class="form-select">
                                        <option value="">Select</option>
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                        <option value="nonbinary">Non-Binary</option>
                                        <option value="other">other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><?php echo __('academic_qualification'); ?></label>
                                    <select name="participant_academic_level" class="form-select">
                                        <option value="">Select...</option>
                                        <?php
                                        $levels = [
                                            'Primary education',
                                            'Secondary education',
                                            'High school diploma',
                                            'Bachelors degree',
                                            'Masters degree',
                                            'Doctorate / PhD',
                                            'Other'
                                        ];
                                        foreach ($levels as $level): ?>
                                            <option value="<?php echo htmlspecialchars($level); ?>">
                                                <?php echo htmlspecialchars($level); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <?php


                            if (!empty($customFields)) { ?>
                                <hr class="my-4">
                                <h5><?php echo __('custom_fields'); ?></h5>
                                <?php foreach ($customFields as $field): ?>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo htmlspecialchars($field['label']); ?></label>
                                        <?php
                                        $value = $customFieldValues[$field['id']] ?? '';
                                        if ($field['field_type'] === 'select') {
                                            echo '<select name="custom_field[' . $field['id'] . ']" class="form-select">';
                                            echo '<option value="">Select...</option>';
                                            foreach (explode(';', $field['options']) as $opt) {
                                                $opt = trim($opt);
                                                $selected = $value === $opt ? 'selected' : '';
                                                echo "<option value=\"$opt\" $selected>$opt</option>";
                                            }
                                            echo '</select>';
                                        } elseif ($field['field_type'] === 'number') {
                                            echo '<input type="number" name="custom_field[' . $field['id'] . ']" class="form-control" value="' . htmlspecialchars($value) . '">';
                                        } else {
                                            echo '<input type="text" name="custom_field[' . $field['id'] . ']" class="form-control" value="' . htmlspecialchars($value) . '">';
                                        }
                                        ?>
                                    </div>
                                <?php endforeach; ?>

                            <?php } else { ?>
                                <div class="alert alert-warning mt-3 mb-3" role="alert">
                                    ⚠️ <?php echo __('no_custom_fields_for_participants_found_for_this_project'); ?>. <a href="#" data-bs-dismiss="modal"><?php echo __('create_custom_fields'); ?></a> <?php echo __('to_collect_additional_information_about_participants'); ?>.
                                </div>
                            <?php } ?>

                            <div class="col-md-12">
                                <?php
                                if (!empty($tests)): ?>
                                    <hr class="my-4">
                                    <h5><?php echo __('tests'); ?></h5>
                                    <p class="text-muted"><?php echo __('select_the_tests_that_this_participant_will_be_assigned_to'); ?></p>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo __('assigned_tests'); ?></label>
                                        <div class="form-check">
                                            <?php foreach ($tests as $test): ?>
                                                <div class="mb-2">
                                                    <input type="checkbox" name="test_ids[]" value="<?php echo $test['id'] ?>"
                                                        class="form-check-input"
                                                        id="test_<?php echo $test['id'] ?>"
                                                        <?php echo in_array($test['id'], $assignedTestIds ?? []) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="test_<?php echo $test['id'] ?>">
                                                        <?php echo htmlspecialchars($test['title']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                    </div>
                                <?php else : ?>
                                    <div class="alert alert-warning mt-3 mb-3" role="alert">
                                        ⚠️ <?php echo __('no_tests_found_for_this_project'); ?>. <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#test-list"><?php echo __('create_tests'); ?></a> <?php echo __('to_assign_to_participants'); ?>.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary"><?php echo __('save_participant'); ?></button>
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
        <!--end::Modal - Create new participant-->



        <?php require __DIR__ . '/../layouts/footer.php'; ?>
        <?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
        </body>

        </html>