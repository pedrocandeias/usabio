<?php 
$menuActive = 'tests';
$title = 'Project details - Task testing';
$pageTitle = 'Project details - Task testing';
$pageDescription = 'Test Sessions for tasks.';
$project_id = $evaluation['project_id'] ?? ($_GET['project_id'] ?? 0);
$headerNavbuttons = [
    'Back to project' => [
        'url' => '/index.php?controller=Test&action=index&project_id='.$project_id,
        'icon' => 'ki-duotone ki-black-left fs-2',
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

<div class="container py-5">

<div class="card mb-4">
    <div class="card-body">
        <?php if (!empty($evaluation['participant_name'])): ?>
            <h1 class="card-title"><?php echo __('task_session'); ?>: <?php echo htmlspecialchars($evaluation['participant_name']); ?></h1>
            <?php if (!empty($customData)): ?>
            <div class="alert alert-light border mb-4">
                <h5 class="mb-3"><?php echo __('participant_info'); ?></h5>
                <ul class="mb-0">
                    <?php foreach ($customData as $entry): ?>
                        <li><strong><?php echo htmlspecialchars($entry['label']); ?>:</strong> <?php echo htmlspecialchars($entry['value']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <h1 class="card-title"><?php echo __('task_session'); ?>: <span class="badge bg-secondary"><?php echo __('anonymous'); ?></span></h1>
        <?php endif; ?>
        <p class="card-text fs-5"><?php echo __('welcome_to_the_task_session_please_follow_the_instructions_below_to_complete_the_tasks'); ?></p>
    </div>
</div>



<form method="POST" action="/index.php?controller=Session&action=saveTaskResponses">
    <input type="hidden" name="evaluation_id" value="<?php echo $evaluation['id']; ?>">

    <?php foreach ($taskGroups as $group): ?>
        <?php if (!empty($group['tasks'])): ?>

            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4"><?php echo htmlspecialchars($group['title']); ?></h3>
                </div>
            </div>

            <div class="row">
            <?php foreach ($group['tasks'] as $task): ?>
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm" id="task-<?php echo $task['id']; ?>">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0"><?php echo htmlspecialchars($task['text']); ?></h3>
                            <?php if (!empty($task['preset_type'])) : ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($task['preset_type']); ?></span>
                            <?php endif; ?>

                            <span class="badge bg-light text-dark">Task ID:</strong> <?php echo htmlspecialchars($task['id']); ?></span>
                        </div>

                        <div class="card-body">
                            <div class="mb-10">
                            <?php if (!empty($task['scenario'])) : ?>
                                <div class="alert alert-secondary fs-3">
                                    <strong><?php echo __('scenario'); ?>:</strong> <?php echo nl2br(htmlspecialchars($task['scenario'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($task['script'])) : ?>
                                <div class="alert alert-info fs-3">
                                    <strong><?php echo __('script'); ?>:</strong> <?php echo nl2br(htmlspecialchars($task['script'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($task['metrics'])) : ?>
                                <div class="alert alert-dark fs-3">
                                    <strong><?php echo __('metrics'); ?>:</strong> <?php echo nl2br(htmlspecialchars($task['metrics'])); ?>
                                </div>
                            <?php endif; ?>
                            </div>

                            <!-- Timer Controls -->
                            <div class="d-flex align-items-center gap-3 flex-wrap my-10">
                                <button type="button" class="btn btn-primary" onclick="startTimer(<?php echo $task['id']; ?>)"><i class="bi bi-play-circle-fill"></i> <?php echo __('start'); ?></button>
                                <button type="button" class="btn btn-warning" onclick="pauseTimer(<?php echo $task['id']; ?>)"><i class="bi bi-pause-circle-fill"></i> <?php __('pause'); ?></button>
                                <span class="fs-3 mx-5">⏱ <span id="time-<?php echo $task['id']; ?>">0</span> <?php echo __('sec'); ?></span>
                                <button type="button" class="btn btn-success" id="btn-<?php echo $task['id']; ?>" onclick="toggleComplete(<?php echo $task['id']; ?>)">
                                <i class="bi bi-check-circle-fill"></i>    
                                <?php echo __('mark_as_complete'); ?>
                                </button>
                                <input type="hidden" name="time_spent[<?php echo $task['id']; ?>]" id="input-<?php echo $task['id']; ?>" value="0">
                            </div>

                           
                            <!-- Answer Input -->
                            <div class="my-10">
                                <?php
                                $type = $task['type'] ?? 'text';
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
                                            <div class="form-check mb-2 form-check-inline">
                                                <input class="form-check-input" type="radio" name="answer[<?php echo $task['id']; ?>]" value="<?php echo $opt['value']; ?>" id="r-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                <label class="form-check-label fs-3 text-black" for="r-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                    <?php echo htmlspecialchars($opt['label']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach;
                                        break;

                                    case 'checkbox':
                                        foreach ($options as $opt): ?>
                                            <div class="form-check mb-2 form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="answer[<?php echo $task['id']; ?>][]" value="<?php echo $opt['value']; ?>" id="c-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                                                <label class="form-check-label fs-3 text-black" for="c-<?php echo $task['id'] . '-' . $opt['value']; ?>"><?php echo $opt['label']; ?></label>
                                            </div>
                                        <?php endforeach;
                                        break;

                                    case 'dropdown': ?>
                                        <select class="form-select" name="answer[<?php echo $task['id']; ?>]">
                                            <?php foreach ($options as $opt): ?>
                                                <option value="<?php echo $opt['value']; ?>"><?php echo $opt['label']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;

                                    default: ?>
                                        <textarea name="answer[<?php echo $task['id']; ?>]" class="form-control" rows="3" placeholder="Participant response..."></textarea>
                                        <?php
                                }
                                ?>
                            </div>

                            <div class="mb-2">
                                <textarea name="notes[<?php echo $task['id']; ?>]" class="form-control" placeholder="Moderator notes..." rows="2"></textarea>
                            </div>
                            <div class="d-flex align-items-center gap-3 flex-wrap my-10">
                                <input class="form-check-input btn-check" type="checkbox" value="Error occurred" id="error-<?php echo $task['id']; ?>" name="errors[<?php echo $task['id']; ?>]">
                                <label class="form-check-label btn btn-danger btn-bordered fs-3" for="error-<?php echo $task['id']; ?>">
                                    <?php echo __('task_failed'); ?>
                                </label>
                            </div>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

        <?php endif; ?>
    <?php endforeach; ?>

    <div class="w-100 mt-4">
        <button type="submit" class="btn btn-success btn-lg w-100"><?php echo __('finish_session'); ?></button>
    </div>
</form>

</div>
</div>
</div>

<script>
const timers = {};
const elapsed = {};

function startTimer(id) {
    if (timers[id]) return;
    timers[id] = setInterval(() => {
        elapsed[id] = (elapsed[id] || 0) + 1;
        document.getElementById(`time-${id}`).textContent = elapsed[id];
        document.getElementById(`input-${id}`).value = elapsed[id];
    }, 1000);
}

function pauseTimer(id) {
    clearInterval(timers[id]);
    delete timers[id];
}

function toggleComplete(id) {
    const card = document.getElementById(`task-${id}`);
    const btn = document.getElementById(`btn-${id}`);
    
    if (!card.classList.contains('completed')) {
        pauseTimer(id);
        card.classList.add('completed', 'bg-light', 'opacity-50');
        btn.textContent = '🔄 Resume Task';
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-outline-secondary');
    } else {
        card.classList.remove('completed', 'bg-light', 'opacity-50');
        btn.textContent = '✅ Mark as Complete';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-outline-success');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
