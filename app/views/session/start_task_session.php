<?php $title = 'Start Task Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Start Task Session</h1>

    <p class="text-muted mb-3">
        <strong>Project:</strong> <?php echo htmlspecialchars($test['project_name']); ?><br>
        <strong>Test:</strong> <?php echo htmlspecialchars($test['title']); ?>
    </p>

    <form method="POST" action="/index.php?controller=Session&action=beginTaskSession">
        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

        <div class="mb-3">
            <label class="form-label">Participant Name</label>
            <input type="text" name="participant_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes / Background</label>
            <textarea name="moderator_observations" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Start Session</button>
        <a href="/index.php?controller=Session&action=dashboard" class="btn btn-secondary">Cancel</a>
    </form>
</div>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
