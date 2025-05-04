<style type="text/css">
    body {
        background-size:cover !important;
        background-repeat: repeat !important;
    }
</style>
<?php 
$title = 'Questionnaire Complete';

require __DIR__ . '/../layouts/header.php'; 

?>
<div class="container py-5 text-center">
    <h1 class="display-5 mb-4 text-white">🎉 <?php echo __('questionnaire_complete'); ?></h1>
    <p class="fs-1 text-white mb-4 my-10">
        <?php echo __('thank_you_for_your_participation'); ?>.
    </p>
    <a href="/index.php?controller=Test&action=index&project_id=<?php echo $project_id; ?>" class="btn btn-light btn-active-light my-10">
        <i class="bi bi-arrow-left-circle-fill me-2 fs-1"></i>
        <?php echo __('back_to_home'); ?>
    </a>
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
