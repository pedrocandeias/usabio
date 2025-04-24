<?php $title = 'Choose a Project Template'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">🎯 Use a Predefined Template</h1>
    <p class="text-muted">Choose one of the templates below to quickly create a new project.</p>

    <div class="row g-4">
        <?php foreach ($templates as $template): ?>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo htmlspecialchars($template['title']); ?></h4>
                        <p class="card-text"><?php echo htmlspecialchars($template['description']); ?></p>
                        <a href="/index.php?controller=Import&action=importTemplate&template=<?php echo $template['id']; ?>" class="btn btn-primary">
                             Create Project from Template
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
