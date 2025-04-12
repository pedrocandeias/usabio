<?php $title = 'Questionnaire Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Questionnaire Session</h1>

    <p class="text-muted mb-3">
        <strong>Project:</strong> <?php echo htmlspecialchars($evaluation['project_name']); ?><br>
        <strong>Test:</strong> <?php echo htmlspecialchars($evaluation['test_title']); ?><br>
        <strong>Participant:</strong>
        <?php echo $evaluation['participant_name'] ? htmlspecialchars($evaluation['participant_name']) : '<span class="badge bg-secondary">Anonymous</span>'; ?>
    </p>

    <?php if (!empty($customData)): ?>
        <div class="alert alert-light border mb-4">
            <h5 class="mb-3">Participant Info</h5>
            <ul class="mb-0">
                <?php foreach ($customData as $entry): ?>
                    <li><strong><?php echo htmlspecialchars($entry['label']); ?>:</strong> <?php echo htmlspecialchars($entry['value']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/index.php?controller=Session&action=saveQuestionnaireResponses">
        <input type="hidden" name="evaluation_id" value="<?php echo $evaluation['id']; ?>">

        <?php foreach ($questionnaireGroups as $group): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo htmlspecialchars($group['title']); ?></h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($group['questions'] as $question): ?>
                        <li class="list-group-item">
                            <div class="mb-2">
                                <strong><?php echo htmlspecialchars($question['text']); ?></strong>
                                <small class="text-muted d-block">[<?php echo $question['question_type']; ?>]</small>
                            </div>

                            <?php
                            $type = $question['question_type'];
                            $options = [];

                            if (!empty($question['question_options'])) {
                                $pairs = explode(';', $question['question_options']);
                                foreach ($pairs as $pair) {
                                    $pair = trim($pair);
                                    if (strpos($pair, ':') !== false) {
                                        [$label, $value] = explode(':', $pair, 2);
                                    } else {
                                        $label = $value = $pair;
                                    }
                                    $options[] = ['label' => trim($label), 'value' => trim($value)];
                                }
                            }

                            switch ($type):
                                case 'radio':
                                    foreach ($options as $opt): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio"
                                                name="answer[<?php echo $question['id']; ?>]"
                                                value="<?php echo $opt['value']; ?>"
                                                id="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                            <label class="form-check-label"
                                                for="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                <?php echo htmlspecialchars($opt['label']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach;
                                    break;

                                case 'checkbox':
                                    foreach ($options as $opt): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="answer[<?php echo $question['id']; ?>][]"
                                                value="<?php echo $opt['value']; ?>"
                                                id="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                            <label class="form-check-label"
                                                for="q-<?php echo $question['id'] . '-' . $opt['value']; ?>">
                                                <?php echo htmlspecialchars($opt['label']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach;
                                    break;

                                case 'dropdown': ?>
                                    <select class="form-select" name="answer[<?php echo $question['id']; ?>]">
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?php echo $opt['value']; ?>"><?php echo $opt['label']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php break;

                                default: ?>
                                    <textarea name="answer[<?php echo $question['id']; ?>]"
                                              class="form-control" rows="3"
                                              placeholder="Participant response..."></textarea>
                            <?php endswitch; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success">Finish Questionnaire</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
