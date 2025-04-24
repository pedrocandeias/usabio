<?php $title = 'Create New Project';
require __DIR__ . '/../layouts/header.php'; 
?>
<div class="container py-5">
    <h1 class="mb-4">Create a New Project</h1>

    <div class="row g-4">

        <!-- Custom Project -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title">🛠️ Custom Project</h4>
                    <p class="card-text">Start from scratch and define all project details manually.</p>
                    <a href="/index.php?controller=Project&action=create" class="btn btn-primary  w-100">Start Manual Setup</a>
                </div>
            </div>
        </div>

        <!-- Import Project -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title">📦 Import Project</h4>
                    <p class="card-text">Upload a JSON file exported from this platform containing a complete project.</p>
                    <a href="/index.php?controller=Import&action=uploadJSONForm" class="btn btn-secondary w-100">Import from File</a>
                </div>
            </div>
        </div>

        <!-- Template Projects -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title">🎯 Use a Template</h4>
                    <p class="card-text">Choose from predefined templates for quick setup (e.g. smart lamp test, onboarding, etc.).</p>
                    <a href="/index.php?controller=Import&action=chooseTemplate" class="btn btn-outline-primary  w-100">Browse Templates</a>
                </div>
            </div>
        </div>

        <!-- AI Generator -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title">🤖 AI Generator</h4>
                    <p class="card-text">Let AI help you generate a project based on a few simple inputs.</p>
                    <a href="/index.php?controller=Import&action=aiForm" class="btn btn-outline-primary w-100">
    🧠 Generate with AI
</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>