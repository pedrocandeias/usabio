<?php 
$projectBase = $project;
$menuActive = 'analysis';
$activeTab = 'questionnaires';
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



       <?php require_once __DIR__  . '/../layouts/analysis-nav.php'; ?>
       
        <div class="card my-5">
            <div class="card-header">
                <h3 class="card-title"><?php echo __('questionnaire_analysis'); ?></h3>
            </div>
        </div>


        <div class="row g-5 g-xl-8">
 
 <?php 
$susQuestions = array_values(array_filter($questionStats, fn($q) => $q['is_sus']));
$otherQuestions = array_values(array_filter($questionStats, fn($q) => !$q['is_sus']));


 if (!empty($susQuestions)): ?>
    <div class="col-12">
        <h2 class="mt-4 mb-3">🧠 <?php echo __('sus_questions'); ?></h2>
        <hr class="border border-primary border-2 opacity-50">
    </div>
    <?php foreach ($susQuestions as $index => $q): ?>
        <div class="col-lg-6 col-md-6">
            <!-- teu código existente para cada pergunta SUS -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-white"><?php echo htmlspecialchars($q['text']); ?></h4>
                    <?php if ($q['inconsistent']): ?>
                        <span class="badge bg-warning text-dark"><?php echo __('high_inconsistency'); ?> (<?php echo __('variance'); ?>: <?php echo $q['variance']; ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($q['counts'])): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="fw-bold text-white bg-dark-subtle">
                                    <th><?php echo __('option'); ?></th>
                                    <th><?php echo __('responses'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($q['counts'] as $value => $count): ?>
                                    <tr>
                                        <td class="w-50"><?php echo htmlspecialchars($q['options'][$value] ?? $value); ?></td>
                                        <td class="w-50"><?php echo $count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                           <div id="sus-chart-<?php echo $index; ?>" style="height: 350px;"></div>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><?php echo __('no_answers_recorded_for_this_question'); ?>.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
      <?php else: ?>
            <div class="alert alert-warning"><?php echo __('no_sus_questionnaire_data_found'); ?>.</div>
        <?php endif; ?>

        <!-- Other Questions -->
        <?php if (!empty($otherQuestions)): ?>
            <div class="col-12">
                <h2 class="mt-5 mb-3">📑 <?php echo __('other_questions'); ?></h2>
                <hr class="border border-secondary border-2 opacity-50">
            </div>
            <?php foreach ($otherQuestions as $index => $q): ?>
                <div class="col-lg-6 col-md-6">
                    <!-- teu código existente para outras perguntas -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="card-title text-white"><?php echo htmlspecialchars($q['text']); ?></h4>
                            <?php if ($q['inconsistent']): ?>
                                <span class="badge bg-warning text-dark"><?php echo __('high_inconsistency'); ?> (<?php echo __('variance'); ?>: <?php echo $q['variance']; ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($q['counts'])): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="fw-bold text-white bg-dark-subtle">
                                            <th>Option</th>
                                            <th>Responses</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($q['counts'] as $value => $count): ?>
                                            <tr>
                                                <td class="w-50"><?php echo htmlspecialchars($q['options'][$value] ?? $value); ?></td>
                                                <td class="w-50"><?php echo $count; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div id="other-chart-<?php echo $index; ?>" style="height: 350px;"></div>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">No answers recorded for this question.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const susQuestions = <?php echo json_encode($susQuestions); ?>;
    const otherQuestions = <?php echo json_encode($otherQuestions); ?>;

    // Função robusta com fallback para cores padrão
    const getCssVar = (varName, fallback) => {
        const val = getComputedStyle(document.documentElement).getPropertyValue(varName);
        return val ? val.trim() : fallback;
    };

    const labelColor = getCssVar('--kt-gray-500', '#7E8299');
    const borderColor = getCssVar('--kt-gray-200', '#E4E6EF');
    const susBaseColor = getCssVar('--kt-primary', '#3E97FF'); // Azul primário como fallback
    const otherBaseColor = getCssVar('--kt-info', '#7239EA');  // Roxo como fallback
    const secondaryColor = getCssVar('--kt-gray-300', '#D9D9D9');

    const createChart = (elementId, question, baseColor) => {
        const element = document.getElementById(elementId);
        if (!element) return;

        const options = {
            series: [{ name: 'Responses', data: Object.values(question.counts) }],
            chart: {
                fontFamily: 'inherit',
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                },
            },
            dataLabels: { enabled: true },
            stroke: { show: true, width: 1, colors: ['transparent'] },
            xaxis: {
                categories: Object.values(question.options),
                labels: { style: { colors: labelColor, fontSize: '12px' } }
            },
            yaxis: {
                labels: { style: { colors: labelColor, fontSize: '12px' } }
            },
            fill: { opacity: 1 },
            tooltip: {
                style: { fontSize: '12px' },
                y: { formatter: val => `${val} responses` }
            },
            colors: [baseColor || susBaseColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            title: {
                text: question.text,
                align: 'left',
                style: { fontSize: '14px', color: labelColor },
                margin: 10
            }
        };

        const chart = new ApexCharts(element, options);
        chart.render();
    };

    // Renderizar gráficos SUS Questions
    susQuestions.forEach((question, index) => {
        createChart(`sus-chart-${index}`, question, susBaseColor);
    });

    // Renderizar gráficos Other Questions
    otherQuestions.forEach((question, index) => {
        createChart(`other-chart-${index}`, question, otherBaseColor);
    });

});
</script>
</body>
</html>
