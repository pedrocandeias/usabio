<?php $title = 'Generate Project with AI'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>

    <h1 class="mb-4">🧠 Generate New Project with AI</h1>
    <div class="mb-3">
        <p class="fw-bold">💡 Example Prompts</p>
        <ul class="small text-muted ps-3">
            <li>Test the usability of a smart home lighting app for elderly users.</li>
            <li>Evaluate how university students navigate a course registration website.</li>
            <li>Assess the learnability of a new wearable fitness tracker for first-time users.</li>
            <li>Design a test for a remote control interface for a smart TV.</li>
            <li>Explore onboarding experiences for a mobile banking application.</li>
        </ul>
    </div>
        <form id="ai-generate-form" method="POST" action="/index.php?controller=Import&action=generateFromPrompt">
            <textarea name="prompt" id="promptInput" class="form-control mb-3" placeholder="Describe your usability test idea..." required></textarea>
        
            <button type="submit" id="generateBtn" class="btn btn-primary">⚡ Generate Project</button>

            <!-- Loading indicator -->
            <div id="loading-indicator" class="mt-4 d-none text-center">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div class="fw-bold">Generating project from AI...</div>
                <div id="jokeArea" class="text-muted mt-2 fst-italic small"></div>
            </div>
        </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
