<?php $title = 'Task Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Task Session: <?php echo htmlspecialchars($evaluation['participant_name']); ?></h1>

    <p class="text-muted mb-3">
        Project: <?php echo htmlspecialchars($evaluation['project_name']); ?><br>
        Test: <?php echo htmlspecialchars($evaluation['test_title']); ?>
    </p>

    <form method="POST" action="/index.php?controller=Session&action=saveTaskResponses">
        <input type="hidden" name="evaluation_id" value="<?php echo $evaluation['id']; ?>">

        <?php foreach ($taskGroups as $group): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo htmlspecialchars($group['title']); ?></h5>
                
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($group['tasks'] as $task): ?>
                        <li class="list-group-item" id="task-<?php echo $task['id']; ?>">
                            <div class="mb-2 d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo htmlspecialchars($task['text']); ?></h3>
                                <!-- Start -->
                                <?php if (!empty($task['scenario'])): ?>
                                        <div class="alert alert-secondary mt-2">
                                            <strong>Scenario:</strong> <?php echo nl2br(htmlspecialchars($task['scenario'])); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($task['script'])): ?>
                                        <div class="alert alert-info">
                                            <strong>Script:</strong> <?php echo nl2br(htmlspecialchars($task['script'])); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($task['metrics'])): ?>
                                        <div class="alert alert-warning">
                                            <strong>Metrics:</strong> <?php echo nl2br(htmlspecialchars($task['metrics'])); ?>
                                        </div>
                                    <?php endif; ?>
                                    <!-- End -->
                                </div>
                    </div>

    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="startTimer(<?php echo $task['id']; ?>)">▶ Start</button>
        <button type="button" class="btn btn-outline-warning btn-sm" onclick="pauseTimer(<?php echo $task['id']; ?>)">⏸ Pause</button>
        <span class="ms-2 fs-5">⏱ <span id="time-<?php echo $task['id']; ?>">0</span> sec</span>
        <input type="hidden" name="time_spent[<?php echo $task['id']; ?>]" id="input-<?php echo $task['id']; ?>" value="0">
    
        <div>
                            <button type="button" class="btn btn-outline-success btn-sm" id="btn-<?php echo $task['id']; ?>" onclick="toggleComplete(<?php echo $task['id']; ?>)">
                                ✅ Mark as Complete
                            </button>
                        </div>
    </div>

    <div class="mb-4 task-option">
                        <?php
                        $type = $task['type'] ?? 'text';
                        $optionsRaw = $task['task_options'] ?? '';
                        $options = [];
                       
                        $type = $task['type'] ?? 'text';
                        $options = [];
                        
                        if (!empty($task['task_options'])) {
                            $pairs = explode(';', $task['task_options']);
                            foreach ($pairs as $pair) {
                                $pair = trim($pair);
                                if (!$pair) continue;
                        
                                if (strpos($pair, ':') !== false) {
                                    [$label, $value] = explode(':', $pair, 2);
                                } else {
                                    $label = $value = $pair;
                                }
                        
                                $options[] = ['label' => trim($label), 'value' => trim($value)];
                            }
                        }

                        switch ($type) {
                        case 'radio':
                            foreach ($options as $opt):
                                ?>
                    <div class="form-check mb-2">
    <input class="form-check-input" type="radio" name="answer[<?= $task['id']; ?>]" value="<?= $opt['value']; ?>" id="r-<?= $task['id'] . '-' . $opt['value']; ?>">
    <label class="form-check-label" for="r-<?= $task['id'] . '-' . $opt['value']; ?>">
        <?= htmlspecialchars($opt['label']); ?>
    </label>
</div>
                                <?php
                            endforeach;
                            break;

                        case 'checkbox':
                            foreach ($options as $opt):
                                ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="answer[<?php echo $task['id']; ?>][]" value="<?php echo $opt['value']; ?>" id="c-<?php echo $task['id'] . '-' . $opt['value']; ?>">
                        <label class="form-check-label" for="c-<?php echo $task['id'] . '-' . $opt['value']; ?>"><?php echo $opt['label']; ?></label>
                    </div>
                                <?php
                            endforeach;
                            break;

                        case 'dropdown':
                            ?>
                <select class="form-select" name="answer[<?php echo $task['id']; ?>]">
                            <?php foreach ($options as $opt): ?>
                        <option value="<?php echo $opt['value']; ?>"><?php echo $opt['label']; ?></option>
                            <?php endforeach; ?>
                </select>
                            <?php
                            break;

                        default: // 'text'
                            ?>
                <textarea name="answer[<?php echo $task['id']; ?>]" class="form-control" rows="3" placeholder="Participant response..."></textarea>
                            <?php
                        }
                        ?>
    </div>


    <textarea name="notes[<?php echo $task['id']; ?>]" class="form-control mt-3" placeholder="Moderator notes..." rows="2"></textarea>
</li>

                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success">Finish Session</button>
    </form>
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
        // Mark as complete and pause timer
        pauseTimer(id);
        card.classList.add('completed', 'bg-light', 'opacity-50');
        btn.textContent = '🔄 Resume Task';
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-outline-secondary');
    } else {
        // Resume
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
