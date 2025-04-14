<?php 
$title = 'Participants';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    
<a href="index.php?controller=Project&action=show&id=<?php echo $projectId; ?>#participant-list" class="btn btn-secondary mb-4">← Back to Projects</a>
   
<h1 class="mb-4">Participants</h1>

    <a href="/index.php?controller=Participant&action=export&project_id=<?php echo $projectId; ?>" class="btn btn-outline-secondary mb-3">📤 Export Participants</a>

    <?php if (!empty($participants)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Academic Level</th>
                        <th>Sessions</th>
                        <th>Last Evaluation</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant['id']); ?></td>
                            <td><?php echo htmlspecialchars($participant['participant_name']); ?></td>
                            <td><?php echo htmlspecialchars($participant['participant_age']); ?></td>
                            <td><?php echo htmlspecialchars($participant['participant_gender']); ?></td>
                            <td><?php echo htmlspecialchars($participant['participant_academic_level']); ?></td>
                            <td><?php echo isset($participant['session_count']) 
                                ? htmlspecialchars($participant['session_count']) 
                                : '<span class="text-muted fst-italic">N/A</span>'; ?></td>
                            <td class="text-muted">
                            <?php echo isset($participant['last_evaluation']) 
                                ? htmlspecialchars($participant['last_evaluation']) 
                                : '<span class="text-muted fst-italic">N/A</span>'; ?>
                            </td>
                            <td><a class="btn btn-sm btn-outline-primary" href="/index.php?controller=Participant&action=show&id=<?php echo $participant['id']; ?>&project_id=<?php echo $projectId; ?>">View</a>
                       
                       
    <a href="/index.php?controller=Participant&action=edit&id=<?php echo $participant['id']; ?>&project_id=<?php echo $projectId; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
    <a href="/index.php?controller=Participant&action=destroy&id=<?php echo $participant['id']; ?>&project_id=<?php echo $projectId; ?>"
       class="btn btn-sm btn-outline-danger"
       onclick="return confirm('Are you sure you want to delete this participant?');">Delete</a>
</td>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No participants found for this project.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
