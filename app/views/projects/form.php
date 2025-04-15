<!-- app/views/projects/form.php -->


<?php 
$title = 'Create/edit project';
require __DIR__ . '/../layouts/header.php'; ?>
<div class="container py-5">
        <h1 class="mb-4"><?php echo $project['id'] ? 'Edit' : 'Create'; ?> Project</h1>

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

            <div class="mb-3">
    <label for="assigned_users" class="form-label">Assign Moderators to this Project</label>
    <select id="assigned_users" name="assigned_users[]" class="form-select" multiple>
        <?php foreach ($allUsers as $user): ?>
            <option value="<?php echo $user['id']; ?>"
                <?php echo in_array($user['id'], $assignedUsers) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($user['username']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">Hold down Ctrl (Windows) or Cmd (Mac) to select multiple users.</small>
</div>

            <button type="submit" class="btn btn-primary"><?php echo $project['id'] ? 'Update' : 'Create'; ?> Project</button>
            <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<!-- Bootstrap 5.3.1 JS Bundle -->

</body>
</html>

