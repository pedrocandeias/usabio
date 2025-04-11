<?php 
$title = 'Test for project';
require __DIR__ . '/../layouts/header.php'; 
?>
<div class="container py-5">

    <!-- Back Button -->
    <a href="/index.php?controller=Project&action=show&id=<?php echo $test['project_id']; ?>" class="btn btn-secondary mb-4">← Back to Project</a>
    <?php if (!empty($test['project_name'])) : ?>
    <p class="text-muted small mb-1">
            Project: <strong><?php echo htmlspecialchars($test['project_name']); ?></strong>
        </p>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <!-- Test Header -->
            <h1 class="mb-3"><?php echo htmlspecialchars($test['title']); ?></h1>

            <?php if (!empty($test['description'])) : ?>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($test['description'])); ?></p>
            <?php endif; ?>
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

    <!-- Task Groups & Tasks -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h2 class="mb-0">Task Groups & Tasks</h2>
        <a href="/index.php?controller=TaskGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-success btn-sm">+ Add Task Group</a>
    </div>

    <?php if (!empty($taskGroups)) : ?>
    <div id="task-group-list">
        <?php foreach ($taskGroups as $group): ?>
        <div class="card mb-4 shadow-sm task-group" data-id="<?php echo $group['id']; ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($group['title']); ?></h5>
                <div>
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

    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h2 class="mb-0">Questionnaire Groups</h2>
        <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>" class="btn btn-success btn-sm">+ Add Questionnaire Group</a>
    </div>

    <div id="questionnaire-group-list">
        <?php if (!empty($questionnaireGroups)) : ?>
            <?php foreach ($questionnaireGroups as $qGroup): ?>
                <div class="card mb-3 shadow-sm questionnaire-group" data-id="<?php echo $qGroup['id']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($qGroup['title']); ?></h5>
                        <div>
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
    <a href="/index.php?controller=Question&action=create&group_id=<?php echo $qGroup['id']; ?>&test_id=<?php echo $test['id']; ?>" class="btn btn-sm btn-success">+ Add Question</a>
</div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No questionnaire groups yet. <a href="/index.php?controller=QuestionnaireGroup&action=create&test_id=<?php echo $test['id']; ?>">Add one</a>.</p>
        <?php endif; ?>
    </div>

</div>

<!-- Reorder Saved Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="savedToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Order saved successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
