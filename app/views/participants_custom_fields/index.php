<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-4">

<a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>" class="btn btn-secondary btn-xs mb-4">
        ← Back to Test
    </a>

<h2>Custom Participant Fields</h2>
<a href="/index.php?controller=ParticipantCustomField&action=create&project_id=<?= $project_id ?>" class="btn btn-primary mb-3">+ Add Field</a>

<table class="table">
    <thead>
        <tr>
            <th>Label</th>
            <th>Type</th>
            <th>Options</th>
            <th>Position</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fields as $field): ?>
            <tr>
                <td><?= htmlspecialchars($field['label']) ?></td>
                <td><?= $field['field_type'] ?></td>
                <td><?= htmlspecialchars($field['options']) ?></td>
                <td><?= $field['position'] ?></td>
                <td>
                    <a href="/index.php?controller=ParticipantCustomField&action=edit&id=<?= $field['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <a href="/index.php?controller=ParticipantCustomField&action=destroy&id=<?= $field['id'] ?>&project_id=<?= $project_id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>" class="btn btn-secondary btn-xs mb-4">
        ← Back to Test
    </a>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
