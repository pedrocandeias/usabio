<?php $title = 'Questionnaire Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
<?php $questionCount = array_reduce($questionnaireGroups, function ($count, $group) {
    return $count + count($group['questions']);
}, 0); ?>

<p>Total Questions: <?php echo $questionCount; ?></p>

    <form method="POST" action="/index.php?controller=Session&action=saveQuestionnaireResponses">
        <input type="hidden" name="evaluation_id" value="<?php echo $evaluation['id']; ?>">

        <?php foreach ($questionnaireGroups as $group): ?>
            <?php foreach ($group['questions'] as $question): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($question['text']); ?></h3>
                </div>
                <ul class="list-group list-group-flush">
                        <li class="list-group-item">
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
    
                </ul>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success">Finish Questionnaire</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
