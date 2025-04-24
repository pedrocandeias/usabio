<?php $title = 'Import Project (JSON)'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">📦 Import Project from JSON</h1>

    <div class="alert alert-info">
        Upload a previously exported project file (<code>.json</code>) that includes project details, tests, task groups, questionnaire groups, and participants.
        <br>        <a href="/index.php?controller=Import&action=downloadExampleProject" class="btn btn-outline-secondary">
                📄 Download Sample JSON
            </a>
    </div>

    <form method="POST" action="/index.php?controller=Import&action=processFile" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Choose JSON file</label>
            <input type="file" name="import_file" accept=".json" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">🚀 Import Project</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
