<?php 
$title = 'Participant: ' . htmlspecialchars($participant['participant_name']);

require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    
    <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id;?>#participant-list" class="btn btn-secondary mb-4">← Back to Projects</a>    

    <h1 class="mb-4">Participant Details</h1>
    <h4 class="mb-3"><?php echo htmlspecialchars($participant['participant_name'] ?? 'Participant'); ?></h4>
    <ul class="list-group mb-4">
        <li class="list-group-item">Age: <strong><?php echo htmlspecialchars($participant['participant_age']); ?></strong></li>
        <li class="list-group-item">Gender: <strong><?php echo htmlspecialchars($participant['participant_gender']); ?></strong></li>
        <li class="list-group-item">Academic Level: <strong><?php echo htmlspecialchars($participant['participant_academic_level']); ?></strong></li>
        <?php foreach ($customData as $label => $value): ?>
            <li class="list-group-item"><?php echo htmlspecialchars($label); ?>: <strong><?php echo htmlspecialchars($value); ?></strong></li>
        <?php endforeach; ?>
    </ul>

    <h5>Evaluations</h5>
    <?php if (!empty($evaluations)): ?>
        <ul class="list-group mb-4">
            <?php foreach ($evaluations as $eval): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?php echo $eval['timestamp']; ?> - Test: <strong><?php echo htmlspecialchars($eval['test_title']); ?></strong></span>
                    <a href="/index.php?controller=Response&action=exportCsv&evaluation_id=<?php echo $eval['id']; ?>" class="btn btn-sm btn-outline-secondary">Export</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No evaluations found.</p>
    <?php endif; ?>

    <a href="/index.php?controller=Project&action=show&id=<?php echo $project_id; ?>" class="btn btn-secondary">← Back to Participant List</a>
</div>



<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
