<?php 
$title = !empty($participant['id']) ? 'Edit Participant' : 'Add Participant';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#participant-list" class="btn btn-secondary mb-4">← Back to Projects</a>
    <h1 class="mb-4"><?php echo $title; ?></h1>

    <form method="POST" action="/index.php?controller=Participant&action=<?php echo !empty($participant_id) ? 'update' : 'store'; ?>">

        <input type="hidden" name="project_id" value="<?php echo $participant_id; ?>">
        <?php if (!empty($participant_id)): ?>
            <input type="hidden" name="participant_id" value="<?php echo $participant_id; ?>">
        <?php endif; ?>
        <?php if (!empty($project_id)): ?>
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Participant Name</label>
            <input type="text" name="participant_name" class="form-control" value="<?php echo htmlspecialchars($participant['participant_name'] ?? ''); ?>" required>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Age</label>
                <input type="number" name="participant_age" class="form-control" value="<?php echo htmlspecialchars($participant['participant_age'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Gender</label>
                <select name="participant_gender" class="form-select">
                    <option value="">Select</option>
                    <option value="female" <?php echo ($participant['participant_gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                    <option value="male" <?php echo ($participant['participant_gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="nonbinary" <?php echo ($participant['participant_gender'] ?? '') === 'nonbinary' ? 'selected' : ''; ?>>Non-Binary</option>
                    <option value="prefer_not_say" <?php echo ($participant['participant_gender'] ?? '') === 'prefer_not_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Academic Qualification</label>
                <select name="participant_academic_level" class="form-select">
                    <option value="">Select...</option>
                    <?php
                    $levels = [
                        'Primary education', 'Secondary education', 'High school diploma',
                        'Bachelors degree', 'Masters degree', 'Doctorate / PhD', 'Other'
                    ];
                    $selected = $participant['participant_academic_level'] ?? '';
                    foreach ($levels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level); ?>" <?php echo ($selected === $level) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level); ?>$customFields
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php 
        
        
        if (!empty($customFields)) { ?>
            <hr class="my-4">
            <h5>Custom Fields</h5>
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
                ⚠️ No custom fields for participants found for this project. <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#custom-fields-list">Create custom fields</a> to collect additional information about participants.
            </div>
        <?php } ?>
       
        <div class="col-md-12">
        <?php if(!empty($tests)): ?>
            <hr class="my-4">
            <h5>Tests</h5>
            <p class="text-muted">Select the tests that this participant will be assigned to.</p>
        <div class="mb-3">
            <label class="form-label">Assigned Tests</label>
            <select name="test_ids[]" class="form-select" multiple>
                <?php foreach ($tests as $test): ?>
                    <option value="<?php echo $test['id'] ?>"
                        <?php echo in_array($test['id'], $assignedTestIds ?? []) ? 'selected' : '' ?>>
                        <?php echo htmlspecialchars($test['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Hold Ctrl / Cmd to select multiple.</div>
        </div>
        <?php else : ?>
            <div class="alert alert-warning mt-3 mb-3" role="alert">
                ⚠️ No tests found for this project. <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#test-list">Create tests</a> to assign to participants.
            </div>
        <?php endif; ?>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">Save Participant</button>
            <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#participant-list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>#participant-list" class="btn btn-secondary mt-4 mb-4">← Back to Projects</a>
 
</div>

<?php if (!empty($_GET['saved'])): ?>
    <!-- Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 mb-5 p-3">
        <div id="savedToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ✅ Participant updated successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
