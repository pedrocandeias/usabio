<?php 
$title = $question['id'] ? 'Edit Question' : 'Create Question';
require __DIR__ . '/../layouts/header.php'; 
?>

    <div class="container py-5">
    <?php if (!empty($context)): ?>
        <a href="/index.php?controller=Test&action=show&id=<?php echo $context['test_id']; ?>#questionnaire-group<?php echo $question['questionnaire_group_id']; ?>" class="btn btn-secondary btn-xs mb-4">
            ← Back to Test
        </a>
    <?php endif; ?>

    <h1 class="mb-4"><?php echo $title; ?></h1>

    <form method="POST" action="/index.php?controller=Question&action=<?php echo $question['id'] ? 'update' : 'store'; ?>">
        <?php if ($question['id']) : ?>
            <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
        <?php endif; ?>

        <input type="hidden" name="questionnaire_group_id" value="<?php echo $question['questionnaire_group_id']; ?>">
        <input type="hidden" name="test_id" value="<?php echo $_GET['test_id'] ?? $_POST['test_id'] ?? ''; ?>">
        <input type="hidden" name="preset_type" id="preset_type" value="<?php echo htmlspecialchars($question['preset_type'] ?? ''); ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Question Text</label>
                <textarea name="text" class="form-control" required rows="4"><?php echo htmlspecialchars($question['text']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Type of Response</label>
                <select name="question_type" id="question_type" class="form-select">
                    <?php foreach (['text', 'radio', 'checkbox', 'dropdown'] as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $question['question_type'] === $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label mt-4">Predefined Evaluation Type (optional)</label>
                <select class="form-select" id="preset-options">
                    <option value="">— Select a common type —</option>
                    <option value="Yes:yes;No:no">Yes / No</option>
                    <option value="Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5">Agreement Scale (1–5)</option>
                    <option value="Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5">Difficulty Scale (1–5)</option>
                    <option value="Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5">Satisfaction Scale (1–5)</option>
                </select>
                <small class="form-text text-muted">This will auto-fill the response type and options below.</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Response Options</label>
            <textarea name="question_options" id="question_options" class="form-control" rows="3"><?php echo htmlspecialchars($question['question_options']); ?></textarea>
            <small class="form-text text-muted">Use <code>Label:Value;Label:Value</code> format for choice-based questions.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" value="<?php echo $question['position']; ?>">
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Question</button>
            <?php
$anchor = $question['id'] ? '#questionnaire-group' . $question['questionnaire_group_id'] : '#questionnaire-group-list';
$cancelUrl = '/index.php?controller=Test&action=show&id=' . $context['test_id'] . $anchor;
?>
<a href="<?php echo $cancelUrl; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const presets = {
    "Yes / No": {
        type: "radio",
        options: "Yes:yes;No:no"
    },
    "Agreement Scale (1-5)": {
        type: "radio",
        options: "Strongly Disagree:1;Disagree:2;Neutral:3;Agree:4;Strongly Agree:5"
    },
    "Difficulty Scale (1-5)": {
        type: "radio",
        options: "Very Easy:1;Easy:2;Neutral:3;Hard:4;Very Hard:5"
    },
    "Satisfaction Scale (1-5)": {
        type: "radio",
        options: "Very Poor:1;Poor:2;Average:3;Good:4;Excellent:5"
    }
};

document.getElementById('preset-options').addEventListener('change', function () {
    const selectedLabel = this.options[this.selectedIndex].text;
    const preset = presets[selectedLabel];

    if (preset) {
        document.getElementById('question_options').value = preset.options;
        document.getElementById('question_type').value = preset.type;
        document.getElementById('preset_type').value = selectedLabel;
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
