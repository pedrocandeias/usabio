<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
$project_id = $evaluation['project_id'] ?? ($_GET['project_id'] ?? 0);
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
       
    <div class="card">
        <div class="card-header">
            <h1 class="mb-4 card-title">Start Task Session</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="/index.php?controller=Session&action=beginTaskSession">
                <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

                <div class="mb-4">
                    <h4>Select Participant</h4>
                    
                    <div class="mb-3">
                        <label for="participant_mode" class="form-label">Who is doing this session?</label>
                        <select id="participant_mode" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php if (!empty($assignedParticipants)) : ?>
                            <option value="assigned">Select assigned participant</option>
                            <?php endif; ?>
                            <option value="custom">Enter custom participant</option>
                            <option value="anonymous">Anonymous participant</option>
                        </select>
                    </div>
                        
                    <!-- Assigned participant dropdown -->
                    <div id="assignedParticipantBlock" class="mb-3 d-none">
                        <label for="participant_id" class="form-label">Select Participant</label>
                        <select name="participant_id" id="participant_id" class="form-select">
                            <option value="">-- Choose Participant --</option>
                            <?php foreach ($assignedParticipants as $participant): ?>
                                <?php
                                
                                $hasDoneTasks = in_array(strtolower($participant['participant_name']), $taskCompletedNames);
                                $hasDoneQuestionnaire = in_array(strtolower($participant['participant_name']), $questionnaireCompletedNames);
                                
                                $label = htmlspecialchars($participant['participant_name']) .
                                    " (" . htmlspecialchars($participant['participant_gender']) . ", " . htmlspecialchars($participant['participant_age']) . ")";
                                
                                if ($hasDoneTasks) {
                                    $label .= " ✅ Task done!";
                                }
                                if ($hasDoneQuestionnaire) {
                                    $label .= " ✅ Questionnaire done!";
                                }
                                ?>
                                
                                <option value="<?php echo $participant['id'] ?>">
                                <?php echo $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                        <?php
                        $fieldIds = array_column($customFields, 'id');
                        ?>
                </div>
                <div id="participantDetails" class="d-none">    
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

                </div>   

                    <div class="mb-3">
                        <label class="form-label">Notes / Background</label>
                        <textarea name="moderator_observations" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Start Session</button>
                    <a href="/index.php?controller=Test&action=index&project_id=<?php echo $test['project_id']; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
   
<script>
    const participants = <?php echo json_encode($assignedParticipants) ?>;
    const customFieldIds = <?php echo json_encode(array_column($customFields, 'id')) ?>;

    const modeSelector = document.getElementById('participant_mode');
    const assignedBlock = document.getElementById('assignedParticipantBlock');
    const participantDetails = document.getElementById('participantDetails');
    const participantSelect = document.getElementById('participant_id');

    const nameInput = document.querySelector('input[name="participant_name"]');
    const ageInput = document.querySelector('input[name="participant_age"]');
    const genderSelect = document.querySelector('select[name="participant_gender"]');
    const academicSelect = document.querySelector('select[name="participant_academic_level"]');

    const generateRandomName = () => 'Participant' + Math.floor(100000 + Math.random() * 900000);

    function clearParticipantFields() {
        nameInput.value = '';
        ageInput.value = '';
        genderSelect.value = '';
        academicSelect.value = '';
        customFieldIds.forEach(fieldId => {
            const input = document.querySelector(`[name="custom_field[${fieldId}]"]`);
            if (input) {
                input.value = '';
            }
        });
    }

    function setParticipantFields(data, lock = false) {
        nameInput.value = data.participant_name || '';
        ageInput.value = data.participant_age || '';
        genderSelect.value = data.participant_gender || '';
        academicSelect.value = data.participant_academic_level || '';

        nameInput.disabled = lock;
        ageInput.disabled = lock;
        genderSelect.disabled = lock;
        academicSelect.disabled = lock;

        const customFields = data.custom_fields || {};
        customFieldIds.forEach(fieldId => {
            const input = document.querySelector(`[name="custom_field[${fieldId}]"]`);
            if (input) {
                input.value = customFields[fieldId] || '';
                input.disabled = lock;
            }
        });
    }

    modeSelector.addEventListener('change', () => {
        const mode = modeSelector.value;
        assignedBlock.classList.add('d-none');
        participantDetails.classList.add('d-none');
        nameInput.disabled = false;
        ageInput.disabled = false;
        genderSelect.disabled = false;
        academicSelect.disabled = false;
        clearParticipantFields();

        if (mode === 'assigned') {
            assignedBlock.classList.remove('d-none');
            participantDetails.classList.remove('d-none');
        } else if (mode === 'custom') {
            participantDetails.classList.remove('d-none');
            customFieldIds.forEach(fieldId => {
                const input = document.querySelector(`[name="custom_field[${fieldId}]"]`);
                if (input) {
                    input.disabled = false;
                }
            });
        } else if (mode === 'anonymous') {
            participantDetails.classList.remove('d-none');
            nameInput.value = generateRandomName();
            nameInput.disabled = true;
            ageInput.disabled = true;
            genderSelect.disabled = true;
            academicSelect.disabled = true;
            customFieldIds.forEach(fieldId => {
                const input = document.querySelector(`[name="custom_field[${fieldId}]"]`);
                if (input) {
                    input.disabled = true;
                }
            });
        }
    });

    participantSelect.addEventListener('change', function () {
        const selectedId = this.value;
        const participant = participants.find(p => p.id == selectedId);
        if (participant) {
            setParticipantFields(participant, true);
        }
    });
</script>

</div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
