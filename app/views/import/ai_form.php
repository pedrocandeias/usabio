<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
$headerNavbuttons = [
    'Back to projects list' => [
        'url' => '/index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_home_primary_button',
    ],
];

require __DIR__ . '/../layouts/header.php'; 
?>

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">

        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">🧠 Generate New Project with AI</h3>
                </div>
       
            <div class="card-body">
            <h4 class="card-subtitle mb-2">Leverage artificial intelligence to create detailed usability test plans tailored to your project needs.<br> Provide a prompt, and let AI do the rest!</h6>
     
                <div class="mb-3">
                    <p class="fw-bold">💡 Example Prompts</p>
                    <ul>
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
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
