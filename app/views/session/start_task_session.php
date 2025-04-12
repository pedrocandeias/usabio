<?php $title = 'Start Task Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Start Task Session</h1>

    <p class="text-muted mb-3">
        <strong>Project:</strong> <?php echo htmlspecialchars($test['project_name']); ?><br>
        <strong>Test:</strong> <?php echo htmlspecialchars($test['title']); ?>
    </p>
    <form method="POST" action="/index.php?controller=Session&action=beginTaskSession">
        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

        <div class="mb-3">
            <label class="form-label">Participant Name</label>
            <input type="text" name="participant_name" class="form-control" value="<?php echo htmlspecialchars($previousEvaluation['participant_name'] ?? ''); ?>" required>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" name="participant_age" class="form-control" value="<?php echo htmlspecialchars($previousEvaluation['participant_age'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="participant_gender" class="form-select">
                        <option value="">Select</option>
                        <option value="female" <?php echo ($previousEvaluation['participant_gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="male" <?php echo ($previousEvaluation['participant_gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="nonbinary" <?php echo ($previousEvaluation['participant_gender'] ?? '') === 'nonbinary' ? 'selected' : ''; ?>>Non-Binary</option>
                        <option value="prefer_not_say" <?php echo ($previousEvaluation['participant_gender'] ?? '') === 'prefer_not_say' ? 'selected' : ''; ?>>Prefer not to say</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Academic Qualification</label>
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
                    $selected = $previousEvaluation['participant_academic_level'] ?? '';
                    foreach ($levels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level); ?>"
                            <?php echo ($selected === $level) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            </div>
        </div>
            <?php if (!empty($customFields)) : ?>
    <hr>
    <h5 class="mt-4">Additional Participant Fields</h5>

                <?php foreach ($customFields as $field): ?>
                    <?php $value = $prefillCustomData[$field['id']] ?? ''; ?>
        <div class="mb-3">
            <label class="form-label"><?php echo htmlspecialchars($field['label']); ?></label>

                    <?php if ($field['field_type'] === 'select') : ?>
                <select class="form-select" name="custom_field[<?php echo $field['id']; ?>]">
                    <option value="">Select...</option>
                        <?php foreach (explode(';', $field['options']) as $option): ?>
                            <?php $option = trim($option); ?>
                        <option value="<?php echo htmlspecialchars($option); ?>" 
                            <?php echo ($option === $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                        </option>
                        <?php endforeach; ?>
                </select>

            <?php elseif ($field['field_type'] === 'number') : ?>
                <input type="number" class="form-control" name="custom_field[<?php echo $field['id']; ?>]" 
                       value="<?php echo htmlspecialchars($value); ?>">

            <?php else: ?>
                <input type="text" class="form-control" name="custom_field[<?php echo $field['id']; ?>]" 
                       value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
        </div>
                <?php endforeach; ?>
            <?php endif; ?>


        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="anonymous" onclick="toggleParticipantName()">
            <label class="form-check-label" for="anonymous">
                This is an anonymous session
            </label>
        </div>

        <script>
        function toggleParticipantName() {
            const checkbox = document.getElementById('anonymous');
            const nameInput = document.querySelector('input[name="participant_name"]');
            const ageInput = document.querySelector('input[name="participant_age"]');
            const genderSelect = document.querySelector('select[name="participant_gender"]');
            const academicInput = document.querySelector('input[name="participant_academic_level"]');

            if (checkbox.checked) {
            nameInput.value = '';
            nameInput.disabled = true;

            ageInput.value = '';
            ageInput.disabled = true;

            genderSelect.value = '';
            genderSelect.disabled = true;

            academicInput.value = '';
            academicInput.disabled = true;
            } else {
            nameInput.disabled = false;
            ageInput.disabled = false;
            genderSelect.disabled = false;
            academicInput.disabled = false;
            }
        }
        </script>

        <div class="mb-3">
            <label class="form-label">Notes / Background</label>
            <textarea name="moderator_observations" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Start Session</button>
        <a href="/index.php?controller=Session&action=dashboard" class="btn btn-secondary">Cancel</a>
    </form>
</div>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
