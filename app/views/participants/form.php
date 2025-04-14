<?php 
$title = !empty($participant['id']) ? 'Edit Participant' : 'Add Participant';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
<a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>
    

    <h1 class="mb-4"><?php echo $title; ?></h1>
    <?php echo $projectId; ?>
    <form method="POST" action="/index.php?controller=Participant&action=store">
        <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">

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
                        'Bachelor’s degree', 'Master’s degree', 'Doctorate / PhD', 'Other'
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

        <?php if (!empty($customFields)) : ?>
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
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Save Participant</button>
        <a href="/index.php?controller=Project&action=show&id=<?php echo $projectId; ?>#participant-list" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
