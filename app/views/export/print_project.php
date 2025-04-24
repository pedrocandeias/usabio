<?php
$title = 'Print Project Template';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <div class="d-print-noned-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">📝 Project Export: <?php echo htmlspecialchars($project['title']) ?></h1>
        <button class="d-print-none btn btn-outline-primary" onclick="window.print()">🖨️ Print</button>
    </div>

    <hr>
    <h4>📄 Project Details</h4>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($project['description'])) ?></p>
    <p><strong>Product under test:</strong> <?php echo htmlspecialchars($project['product_under_test']) ?></p>
    <p><strong>Objectives:</strong> <?php echo nl2br(htmlspecialchars($project['test_objectives'])) ?></p>
    <p><strong>Location & Dates:</strong> <?php echo nl2br(htmlspecialchars($project['location_dates'])) ?></p>
    <p><strong>Procedure:</strong> <?php echo nl2br(htmlspecialchars($project['test_procedure'])) ?></p>

    <hr>
    <h4>🧪 Task Groups & Tasks</h4>
    <?php foreach ($taskGroups as $group): ?>
        <h5 class="mt-4"><?php echo htmlspecialchars($group['title']) ?></h5>
        <ul class="list-group mb-3">
            <?php foreach ($tasksByGroup[$group['id']] as $task): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($task['task_text']) ?></strong><br>
                    <small class="text-muted">Type: <?php echo $task['task_type'] ?> | Preset: <?php echo $task['preset_type'] ?></small>
                    <?php if (!empty($task['script'])) : ?><br><em>Script:</em> <?php echo nl2br(htmlspecialchars($task['script'])) ?><?php 
                    endif; ?>
                    <?php if (!empty($task['scenario'])) : ?><br><em>Scenario:</em> <?php echo nl2br(htmlspecialchars($task['scenario'])) ?><?php 
                    endif; ?>
                    <?php if (!empty($task['metrics'])) : ?><br><em>Metrics:</em> <?php echo nl2br(htmlspecialchars($task['metrics'])) ?><?php 
                    endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <hr>
    <h4>🧠 Questionnaire Groups & Questions</h4>
    <?php foreach ($questionnaireGroups as $group): ?>
        <h5 class="mt-4"><?php echo htmlspecialchars($group['title']) ?></h5>
        <ul class="list-group mb-3">
            <?php foreach ($questionsByGroup[$group['id']] as $q): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($q['text']) ?></strong><br>
                    <small class="text-muted">Type: <?php echo $q['question_type'] ?><?php if ($q['preset_type']) : ?> | Preset: <?php echo $q['preset_type'] ?><?php 
                   endif; ?></small>
                    <?php if (!empty($q['question_options'])) : ?><br><em>Options:</em> <?php echo htmlspecialchars($q['question_options']) ?><?php 
                    endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <hr>
    <h4>👤 Participants</h4>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Academic Level</th>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
