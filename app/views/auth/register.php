<?php
// app/views/auth/register.php
$error = $_GET['error'] ?? null;
$pageTitle = "Register";
$pageDescription = "Create a new account";
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
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">

                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
<form class="form w-100" method="POST" action="/?controller=Auth&action=storeRegistration" id="kt_sign_up_form">
    <!--begin::Heading-->
    <div class="text-center mb-11">
        <h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
        <div class="text-gray-500 fw-semibold fs-6">Create your moderator account</div>
    </div>

    <!--begin::Input group=-->
    <div class="fv-row mb-8">
        <input type="email" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" required />
    </div>

    <div class="fv-row mb-8" data-kt-password-meter="true">
        <div class="mb-1">
            <div class="position-relative mb-3">
                <div class="text-muted py-2 px-2">Use 8 or more characters with a mix of letters, numbers & symbols.</div>
                <input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off" required />
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                    <i class="ki-duotone ki-eye-slash fs-2"></i>
                    <i class="ki-duotone ki-eye fs-2 d-none"></i>
                </span>
            </div>
            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                <div class="flex-grow-1 bg-light bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-light bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-light bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-light bg-active-success rounded h-5px"></div>
            </div>
        </div>
    </div>

    <div class="fv-row mb-8">
        <input placeholder="Repeat Password" name="confirm_password" type="password" autocomplete="off" class="form-control bg-transparent" required />
    </div>

    <div class="fv-row mb-8">
        <label class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="toc" value="1" required />
            <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">I Accept the 
            <a href="#" class="ms-1 link-primary">Terms</a></span>
        </label>
    </div>

    <div class="d-grid mb-10">
        <button type="submit"  class="btn btn-primary">
            <span class="indicator-label">Sign up</span>
            <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>

    <div class="text-gray-500 text-center fw-semibold fs-6">
        Already have an Account?
        <a href="index.php?controller=Auth&action=login" class="link-primary fw-semibold">Sign in</a>
    </div>
                        </form>
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
<script src="assets/js/custom/authentication/sign-up/general.js"></script>
</body>
</html>
