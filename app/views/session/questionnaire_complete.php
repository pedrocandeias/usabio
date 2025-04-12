<?php 
$title = 'Questionnaire Complete';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5 text-center">
    <h1 class="display-5 mb-4">🎉 Questionnaire Complete</h1>
    <p class="lead text-muted mb-4">
        Thank you for your participation.
    </p>
    <a href="/index.php" class="btn btn-outline-secondary">← Back to Home</a>
</div>

<script type="module">
    import 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js';

    setTimeout(() => {
        window.confetti({
            particleCount: 180,
            spread: 75,
            origin: { y: 0.6 }
        });
    }, 300);
</script>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
