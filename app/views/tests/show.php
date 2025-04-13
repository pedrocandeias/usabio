<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">

    <?php if (!empty($test)) : ?>
        <a href="/index.php?controller=Project&action=show&id=<?php echo $test['project_id']; ?>" class="btn btn-secondary btn-xs mb-4">
            ← Back to Project
        </a>
        <p class="text-muted mb-4">
            <strong>Project:</strong> <?php echo htmlspecialchars($test['project_name']); ?>
        </p>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($test['title']); ?></h1>

            <?php if (!empty($test['description'])) : ?>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($test['description'])); ?></p>
            <?php endif; ?>

            <a href="/index.php?controller=Response&action=exportCsv&test_id=<?php echo $test['id']; ?>" class="btn btn-outline-secondary btn-sm">
                📤 Download All Responses (CSV)
            </a>
        </div>

        <div class="col-md-6">
            <?php if (!empty($test['layout_image'])) : ?>
                <div class="mb-5 text-center">
                    <a href="uploads/<?php echo htmlspecialchars($test['layout_image']); ?>">
                        <img src="uploads/<?php echo htmlspecialchars($test['layout_image']); ?>" alt="Layout image" class="img-fluid rounded shadow-sm" style="max-width: 100%;">
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Task Groups -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h2 class="mb-0">Task Groups & Tasks</h2>
        <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-success btn-sm">+ Add Task Group</a>
    </div>

    <?php if (!empty($taskGroups)) : ?>
        <div id="task-group-list">
            <?php foreach ($taskGroups as $group): ?>
                <div class="card mb-4 shadow-sm task-group" id="taskgroup<?php echo $group['id']; ?>" data-id="<?php echo $group['id']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($group['title']); ?></h5>
                        <div>
                            <a href="/index.php?controller=Response&action=exportCsvByTaskGroup&group_id=<?php echo $group['id']; ?>" class="btn btn-outline-secondary btn-sm">📥 Export answers</a>
                            <a href="/index.php?controller=TaskGroup&action=edit&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary">Edit Group</a>
                            <a href="/index.php?controller=TaskGroup&action=destroy&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this group?');">Delete</a>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush task-list" data-group-id="<?php echo $group['id']; ?>">
                        <?php if (!empty($group['tasks'])) : ?>
                            <?php foreach ($group['tasks'] as $task): ?>
                                <li class="list-group-item d-flex justify-content-between task-item" data-id="<?php echo $task['id']; ?>">
                                    <div>
                                        <strong><?php echo htmlspecialchars($task['task_text']); ?></strong>
                                        <small class="text-muted">[<?php echo $task['task_type']; ?>]</small>
                                    </div>
                                    <div>
                                        <a href="/index.php?controller=Task&action=edit&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="/index.php?controller=Task&action=destroy&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this task?');">Delete</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">No tasks in this group yet.</li>
                        <?php endif; ?>
                    </ul>

                    <div class="card-footer text-end">
                        <a href="/index.php?controller=Task&action=create&group_id=<?php echo $group['id']; ?>" class="btn btn-sm btn-success">+ Add Task</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No task groups yet. <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo $test['id']; ?>">Add one</a>.</p>
    <?php endif; ?>

    <!-- Questionnaire Groups -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h2 class="mb-0">Questionnaire Groups</h2>
        <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-success btn-sm">+ Add Questionnaire Group</a>
    </div>

    <div id="questionnaire-group-list">
     
        <?php if (!empty($questionnaireGroups)) : ?>
            <?php foreach ($questionnaireGroups as $qGroup): ?>
                <div class="card mb-3 shadow-sm questionnaire-group" id="questionnaire-group<?php echo $qGroup['id']; ?>" data-id="<?php echo $qGroup['id']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($qGroup['title']); ?></h5>
                        <div>
                            <a href="/index.php?controller=Response&action=exportCsvByQuestionnaireGroup&group_id=<?php echo $qGroup['id']; ?>" class="btn btn-outline-secondary btn-sm">📥 Export answers</a>
                            <a href="/index.php?controller=QuestionnaireGroup&action=edit&id=<?php echo $qGroup['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="/index.php?controller=QuestionnaireGroup&action=destroy&id=<?php echo $qGroup['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this group?');">Delete</a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <?php if (!empty($qGroup['questions'])) : ?>
                            <ul class="list-group list-group-flush question-list" data-group-id="<?php echo $qGroup['id']; ?>">
                                <?php foreach ($qGroup['questions'] as $question): ?>
                                    <li class="list-group-item d-flex justify-content-between question-item" data-id="<?php echo $question['id']; ?>">
                                        <div>
                                            <strong><?php echo htmlspecialchars($question['text']); ?></strong>
                                            <small class="text-muted">[<?php echo $question['question_type']; ?>]</small>
                                        </div>
                                        <div>
                                            <a href="/index.php?controller=Question&action=edit&id=<?php echo $question['id']; ?>&test_id=<?php echo $test['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="/index.php?controller=Question&action=destroy&id=<?php echo $question['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this question?');">Delete</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="p-3 text-muted">No questions yet.</p>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer text-end">
                        <a href="/index.php?controller=Question&action=generateSUS&group_id=<?php echo $qGroup['id']; ?>&test_id=<?php echo $test['id']; ?>" 
                           class="btn btn-outline-secondary btn-sm"
                           onclick="return confirm('Generate SUS questions in this group? This will add 10 questions.')">
                            🧠 Add SUS Questions
                        </a>
                        <a href="/index.php?controller=Question&action=create&group_id=<?php echo $qGroup['id']; ?>&test_id=<?php echo $test['id']; ?>" class="btn btn-sm btn-success">+ Add Question</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No questionnaire groups yet. <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>">Add one</a>.</p>
        <?php endif; ?>
    </div>

    <!-- Custom Participant Fields -->
    <div id="custom-fields-list">
        <hr class="my-5">
        <h4 class="mb-3">🧬 Custom Participant Fields</h4>

        <form method="POST" action="/index.php?controller=CustomField&action=store" class="row g-3 mb-4">
            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

            <div class="col-md-4">
                <input type="text" name="label" class="form-control" placeholder="Field Label" required>
            </div>

            <div class="col-md-4">
                <select name="field_type" class="form-select" required>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Dropdown (select)</option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="options" class="form-control" placeholder="Options (for select, e.g. A;B;C)">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-success w-100">Add</button>
            </div>
        </form>

        <?php
        $stmt = $this->pdo->prepare("SELECT * FROM test_custom_fields WHERE test_id = ? ORDER BY position ASC");
        $stmt->execute([$test['id']]);
        $customFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($customFields)) : ?>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Options</th>
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
                                <a href="/index.php?controller=CustomField&action=destroy&id=<?php echo $field['id']; ?>&test_id=<?php echo $test['id']; ?>"
                                class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this field?')">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No custom fields defined yet for this test.</p>
        <?php endif; ?>
    </div>
</div> <!-- Close .container -->

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="savedToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">Order saved successfully!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
