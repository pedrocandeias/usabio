<?php 
$title = 'Admin Dashboard';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <h1 class="mb-4">📊 Admin Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-light mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Sessions</h5>
                    <p class="display-6"><?php echo $totalEvaluations; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Responses</h5>
                    <p class="display-6"><?php echo $totalResponses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Avg. Task Time</h5>
                    <p class="display-6"><?php echo $avgTime; ?> sec</p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Question Types Distribution</h4>
    <canvas id="questionTypeChart" height="120"></canvas>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('questionTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($questionTypes, 'question_type')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($questionTypes, 'count')); ?>,
                backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?> 
</body>
</html>