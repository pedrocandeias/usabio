<?php 
$projectBase = $project;
$menuActive = 'analysis';
$title = 'Questionnaire Analysis';
$pageTitle = 'Questionnaire Analysis';
$pageDescription = 'Review participant answers to questionnaire items.';
$headerNavbuttons = [
    'Back to project' => [
        'url' => '/index.php?controller=Project&action=show&id=' . $project['id'],
        'icon' => 'ki-duotone ki-black-left fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
    ],
];
require __DIR__ . '/../layouts/header.php'; 
?>

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>

        <h3 class="fw-bold my-4">Questionnaire Analysis for <?php echo htmlspecialchars($project['title']); ?></h3>

        <?php if (!empty($questionStats)): ?>
            <?php foreach ($questionStats as $q): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo htmlspecialchars($q['text']); ?></h4>
                        <?php if ($q['inconsistent']): ?>
                            <span class="badge bg-warning text-dark">High inconsistency (variance: <?php echo $q['variance']; ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($q['counts'])): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Option</th>
                                        <th>Responses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($q['counts'] as $value => $count): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($q['options'][$value] ?? $value); ?></td>
                                            <td><?php echo $count; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No answers recorded for this question.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">No questionnaire data found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
