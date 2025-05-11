<?php require __DIR__ . '/../layouts/header.php'; ?>



<div class="container py-4">




<h2><?= $field ? 'Edit' : 'Add' ?> Custom Field</h2>

<form method="POST" action="/index.php?controller=ParticipantCustomField&action=<?= $field ? 'update' : 'store' ?>">
    <?php if ($field): ?>
        <input type="hidden" name="id" value="<?= $field['id'] ?>">
    <?php endif; ?>
    <input type="hidden" name="project_id" value="<?= $_GET['project_id'] ?? $field['project_id'] ?>">

    <div class="mb-3">
        <label class="form-label">Label</label>
        <input type="text" name="label" class="form-control" required value="<?= htmlspecialchars($field['label'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Field Type</label>
        <select name="field_type" class="form-select">
            <option value="text" <?= ($field['field_type'] ?? '') === 'text' ? 'selected' : '' ?>>Text</option>
            <option value="number" <?= ($field['field_type'] ?? '') === 'number' ? 'selected' : '' ?>>Number</option>
            <option value="select" <?= ($field['field_type'] ?? '') === 'select' ? 'selected' : '' ?>>Dropdown</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Options (for dropdown; use `;` to separate)</label>
        <input type="text" name="options" class="form-control" value="<?= htmlspecialchars($field['options'] ?? '') ?>">
    </div>

   
        <input type="hidden" name="position" class="form-control" value="<?= htmlspecialchars($field['position'] ?? 0) ?>">
 

    <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
    <a href="/index.php?controller=ParticipantCustomField&action=index&project_id=<?= $_GET['project_id'] ?? $field['project_id'] ?>" class="btn btn-secondary">Cancel</a>
</form>



</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
