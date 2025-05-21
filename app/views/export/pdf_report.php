<?php
// Expect variables: $project, $aiSummary, $taskStats, $problematicTasks, $questionStats, $susBreakdown, $participants, $customFields, $correlationData

function safe($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Usability Report: <?php echo safe($project['title']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        h1, h2, h3, h4 { color: #173F5F; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ccc; padding: 4px 8px; text-align: left; }
        th { background: #E8F1FA; }
        .section { margin-top: 36px; }
        .problem { color: #c1121f; font-weight: bold; }
        .badge { display: inline-block; padding: 1px 7px; background: #eee; border-radius: 3px; margin-right: 4px; }
        .table-summary th, .table-summary td { border: none; }
    </style>
</head>
<body>
    <h1>Usability Report: <?php echo safe($project['title']); ?></h1>
    <hr />

    <h2>Project Summary</h2>
    <table class="table-summary">
        <tr><th>Project</th><td><?php echo safe($project['title']); ?></td></tr>
        <tr><th>Description</th><td><?php echo safe($project['description']); ?></td></tr>
        <tr><th>Date</th><td><?php echo date('Y-m-d'); ?></td></tr>
    </table>

    <?php if (!empty($aiSummary['ai_summary'])): ?>
        <div class="section">
            <h2>AI-Generated Usability Summary</h2>
            <div style="background:#F4F6FA; padding:16px; border-radius:6px;"><?php echo nl2br(safe($aiSummary['ai_summary'])); ?></div>
            <div style="font-size:10px; color:#777;">Generated on: <?php echo safe($aiSummary['last_updated']); ?></div>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Task Analytics</h2>
        <table>
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Success %</th>
                    <th>Fail %</th>
                    <th>Skipped %</th>
                    <th>Median Time (s)</th>
                    <th>Std. Dev Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taskStats as $task): ?>
                <tr>
                    <td><?php echo safe($task['task_text']); ?></td>
                    <td><?php echo $task['success_rate']; ?></td>
                    <td><?php echo $task['fail_rate']; ?></td>
                    <td><?php echo $task['skipped_rate'] ?? '-'; ?></td>
                    <td><?php echo $task['median_time']; ?></td>
                    <td><?php echo $task['stddev_time']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($problematicTasks)): ?>
    <div class="section">
        <h3>🚨 Predictive Alerts: Problematic Tasks</h3>
        <table>
            <thead>
                <tr>
                    <th>Task</th><th>Fail %</th><th>Success %</th><th>Std Dev</th><th>Reasons</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($problematicTasks as $task): ?>
                <tr>
                    <td><?php echo safe($task['task_text']); ?></td>
                    <td><?php echo $task['fail_rate']; ?></td>
                    <td><?php echo $task['success_rate']; ?></td>
                    <td><?php echo $task['stddev_time']; ?></td>
                    <td><?php echo implode('; ', $task['reasons']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>Questionnaire Analysis</h2>
        <?php foreach ($questionStats as $q): ?>
            <div style="margin-bottom:14px;">
                <h4><?php echo safe($q['text']); ?> <?php if (!empty($q['is_sus'])) echo '<span class="badge">SUS</span>'; ?></h4>
                <table>
                    <thead>
                        <tr><th>Option</th><th>Responses</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($q['counts'] as $opt => $cnt): ?>
                            <tr>
                                <td><?php echo safe($q['options'][$opt] ?? $opt); ?></td>
                                <td><?php echo $cnt; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!empty($q['inconsistent'])): ?>
                    <span class="problem">⚠ High answer variance (variance: <?php echo $q['variance']; ?>)</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($susBreakdown)): ?>
    <div class="section">
        <h2>SUS (System Usability Scale) Scores</h2>
        <table>
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>SUS Score</th>
                    <?php for ($i=1; $i<=10; $i++) echo "<th>Q$i</th>"; ?>
                    <th>Label</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($susBreakdown as $row): ?>
                <tr>
                    <td><?php echo safe($row['participant']); ?></td>
                    <td><?php echo $row['score']; ?></td>
                    <?php foreach ($row['answers'] as $ans): ?>
                        <td><?php echo $ans; ?></td>
                    <?php endforeach; ?>
                    <td><?php echo $row['label']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>Participants</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Age</th><th>Gender</th><th>Academic Level</th>
                    <th>Tasks Completed</th><th>Task Success %</th><th>Questionnaire Completed</th><th>SUS Score</th>
                    <?php foreach ($customFields as $field): ?>
                        <th><?php echo safe($field['label']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $p): ?>
                <tr>
                    <td><?php echo safe($p['participant_name']) ?: 'Anonymous'; ?></td>
                    <td><?php echo safe($p['participant_age']); ?></td>
                    <td><?php echo safe($p['participant_gender']); ?></td>
                    <td><?php echo safe($p['participant_academic_level']); ?></td>
                    <td><?php echo $p['tasks_completed'] ?? 0; ?></td>
                    <td><?php echo $p['task_success'] ?? 0; ?>%</td>
                    <td><?php echo !empty($p['questionnaire_completed']) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $p['sus_score'] !== null ? $p['sus_score'] : '-'; ?></td>
                    <?php foreach ($customFields as $field): ?>
                        <td><?php echo safe($p['custom_fields'][$field['label']] ?? '-'); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($correlationData)): ?>
    <div class="section">
        <h2>Demographic Correlation Analysis</h2>
        <?php foreach ($correlationData as $groupType => $groups): ?>
            <h4><?php echo ucfirst(str_replace('_', ' ', $groupType)); ?></h4>
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Participants</th>
                        <th>Avg Task Success %</th>
                        <th>Avg SUS</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $val => $stats): ?>
                    <tr>
                        <td><?php echo safe($val); ?></td>
                        <td><?php echo $stats['count'] ?? 0; ?></td>
                        <td><?php echo $stats['avg_task_success'] ?? '-'; ?>%</td>
                        <td><?php echo $stats['avg_sus'] ?? '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</body>
</html>
