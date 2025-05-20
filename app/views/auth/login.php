<?php
// app/views/auth/login.php
$error = $_GET['error'] ?? null;
$pageTitle = "Login";
$pageDescription = "Login to your account";

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

?>

<?php require __DIR__ . '/../layouts/head.php'; ?>

<body id="kt_body" class="auth-bg bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid flex-lg-row">
            <!-- Left Section -->
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <div class="d-flex flex-center flex-lg-start flex-column">
                    <a href="/" class="mb-7">
                        <img alt="Logo" src="/assets/media/logos/logo-white-login.svg" />
                    </a>
                    <h2 class="text-white fw-normal m-0">Industrial Design Usability Assessments</h2>
                </div>
            </div>

            <!-- Right Section (Form) -->
            <div class="d-flex align-items-center flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">

                 
                  <?php if ($success === 'registered'): ?> 
                    <div class="alert alert-success fs-4">
                        ✅ Your email has been confirmed. You may now log in.
                    </div>
                <?php endif; ?>
                <?php if ($success === 'confirm_required'): ?>
                    <div class="alert alert-success fs-4">
                        ✅ Your account has been created. <strong>Please check your email to activate it</strong>.
                    </div>
                <?php endif; ?>
                
                <?php if ($error === 'confirm_required'): ?>
                    <div class="alert alert-warning fs-4">
                        ⚠️ <strong>Please confirm your email address before logging in.</strong>
                    </div>
                <?php endif; ?>
                <?php if ($error === 'Invalid credentials'): ?>
                    <div class="alert alert-danger fs-4">
                        ⚠️ Invalid credentials, please try again.        
                    </div>
                <?php endif; ?>


                    <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                        <form class="form w-100" method="POST" action="/?controller=Auth&action=processLogin" id="kt_sign_in_form">
                            <div class="py-5">
                                <h1 class="text-gray-900 fw-bolder">Sign in</h1>
                            </div>

                            <div class="fv-row mb-8">
                                <input type="text" name="username" placeholder="Username" autocomplete="off" class="form-control bg-transparent" required />
                            </div>

                            <div class="fv-row mb-3">
                                <input type="password" name="password" placeholder="Password" autocomplete="off" class="form-control bg-transparent" required />
                            </div>

                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Login</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                        <p>Don't have an account? <a href="index.php?controller=Auth&action=register">Register here</a></p>

                    </div>

                    <div class="d-flex flex-stack px-lg-10">
                        <div class="d-flex fw-semibold text-primary fs-base gap-5">
                            <a href="#" target="_blank">Terms</a>
                            <a href="#" target="_blank">Contact</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>
