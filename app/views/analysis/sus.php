<?php 
$projectBase = $project;
$menuActive = 'analysis';
$activeTab = 'sus';
$title = 'SUS Analysis';
$pageTitle = 'SUS Analysis';
$pageDescription = 'System Usability Scale (SUS) scores for participants.';
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


      <?php require_once __DIR__  . '/../layouts/analysis-nav.php'; ?>
       

        <div class="card my-5">
            <div class="card-header">
                <h3 class="card-title">SUS Analysis for <?php echo htmlspecialchars($project['title']); ?></h3>
            </div>
            <div class="card-body">
                <?php if (!empty($susSummary)): ?>
                    <div class="alert alert-info fs-3">
                        <strong>Average SUS Score:</strong> <?php echo $susSummary['average']; ?> (<?php echo $susSummary['label']; ?>)<br>
                        <strong>Variation:</strong> <?php echo ucfirst($susSummary['variation']); ?><br>
                        <strong>Participants below 50 (Poor):</strong> <?php echo $susSummary['low']; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($susBreakdown)): ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th>SUS Score</th>
                                <th>Label</th>
                                <th>Answers (Q1 → Q10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($susBreakdown as $entry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($entry['participant']); ?></td>
                                    <td><?php echo $entry['score']; ?></td>
                                    <td><?php echo $entry['label']; ?></td>
                                    <td>
                                        <?php echo implode(', ', $entry['answers']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning">No SUS data found for this project.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
