<?php $title = 'Start Task Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
       <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>    
    </div>
       
<div class="container py-5">
    <h1>Duplicate a Project</h1>
    <form method="GET" action="/index.php">
        <input type="hidden" name="controller" value="Duplicate">
        <input type="hidden" name="action" value="duplicateProject">
        <div class="mb-3">
            <label for="project_id" class="form-label">Select Project</label>
            <select name="id" id="project_id" class="form-select" required>
                <?php foreach ($projects as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Duplicate Selected Project</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

