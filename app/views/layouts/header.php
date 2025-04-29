<?php  
if ($_SESSION['is_superadmin'] == 1) {
    $user_type = 'Super Admin';
} elseif ($_SESSION['is_admin'] == 1) {
    $user_type = 'Admin';
} else {
    $user_type = 'User';
} ?>
<?php
if (!empty($_SESSION['fullname']) ) {
    $displayName = $_SESSION['fullname'];
} elseif (!empty($_SESSION['username'])) {
    $displayName= $_SESSION['username'];
} else {
    $displayName = 'Guest';
}
?>

<?php require __DIR__ . '/../layouts/head.php';  ?>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">

<?php if (empty($minimalLayout)) : ?>

<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                <div id="kt_header" class="header align-items-stretch mb-5 mb-lg-10" data-kt-sticky="true" data-kt-sticky-name="header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
                    <!--begin::Container-->
                    <div class="container-xxl d-flex align-items-center">
                        <!--begin::Heaeder menu toggle-->
                        <div class="d-flex topbar align-items-center d-lg-none ms-n2 me-3" title="Show aside menu">
                            <div class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                                <i class="ki-duotone ki-abstract-14 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        
                        <!--end::Heaeder menu toggle-->
                        <!--begin::Header Logo-->
                        <div class="header-logo me-5 me-md-10 flex-grow-1 flex-lg-grow-0">
                            <a href="/?controller=Project&action=index">
                                <img alt="Logo" src="assets/media/logos/logo-white.svg" class="logo-default h-55px" />
                                <img alt="Logo" src="assets/media/logos/logo-primary.svg" class="logo-sticky h-55px" />
                            </a>
                        </div>
                        <!--end::Header Logo-->
                        <!--begin::Wrapper-->

                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                            <!--begin::Navbar-->
                            <div class="d-flex align-items-stretch" id="kt_header_nav">
                                <!--begin::Menu wrapper-->
                                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                                    <!--begin::Menu-->
                                    <div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-700 menu-state-primary menu-arrow-gray-500 fw-semibold my-5 my-lg-0 align-items-stretch px-2 px-lg-0" id="#kt_header_menu" data-kt-menu="true">
                                        
                                        <div class="menu-item  here menu-here-bg me-0 me-lg-2">
                                           
                                            <a class="menu-link py-3" href="/?controller=Project&action=index">
                                                <span class="menu-title">Projects</span>
                                                <span class="menu-arrow d-lg-none"></span>
                                            </a>
                                           
                                        </div>
                                        
                                        
                                        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
                                           
                                            <a class="menu-link py-3" href="/?controller=Project&action=index">
                                                <span class="menu-title">Team</span>
                                                <span class="menu-arrow d-lg-none"></span>
                                            </a>
                                           
                                        </div>
                                        
                                    </div>
                                    <!--end::Menu-->
                                </div>
                                <!--end::Menu wrapper-->
                            </div>
                            <!--end::Navbar-->
                            <!--begin::Toolbar wrapper-->
                            <div class="topbar d-flex align-items-stretch flex-shrink-0">
                
                            <?php if (!empty($_SESSION['is_superadmin'])) : ?>
                                <!--begin::Admin links-->
                                <div class="d-flex align-items-center ms-1 ms-lg-3">
                                    <!--begin::Menu wrapper-->
                                    <div class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <i class="ki-duotone ki-element-11 fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </div>      
                                <!--begin::Menu-->

                        <div class="menu menu-sub menu-sub-dropdown menu-column w-250px w-lg-325px" data-kt-menu="true">
                            <!--begin::Heading-->
                            <div class="d-flex flex-column flex-center bgi-no-repeat rounded-top px-9 py-10" style="background-image:url('assets/media/misc/menu-header-bg.jpg')">
                                <!--begin::Title-->
                                <h3 class="text-white fw-semibold mb-3">Administration</h3>
                                <!--end::Title-->
                            </div>
                            <!--end::Heading-->
                            <!--begin:Nav-->
                            <div class="row g-0">
                                <!--begin:Item-->
                                <div class="col-6">
                                    <a href="/index.php?controller=User&action=index" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom">
                                        <i class="ki-duotone ki-user fs-3x text-primary mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Users</span>
                                    </a>
                                </div>
                                <!--end:Item-->
                                <!--begin:Item-->
                                <div class="col-6">
                                    <a href="https://usabio.ddev.site:8443/index.php?controller=Settings&action=index" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-bottom">
                                        <i class="ki-duotone ki-gear fs-3x text-primary mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Settings</span>
                                    </a>
                                </div>
                                <!--end:Item-->
                                <!--begin:Item-->
                                <div class="col-6">
                                    <a href="" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end">
                                        <i class="ki-duotone ki-abstract-41 fs-3x text-primary mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Projects</span>
                                    </a>
                                </div>
                                <!--end:Item-->
                                <!--begin:Item-->
                                <div class="col-6">
                                    <a href="" class="d-flex flex-column flex-center h-100 p-6 bg-hover-light">
                                        <i class="ki-duotone ki-graph fs-3x text-primary mb-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fs-5 fw-semibold text-gray-800 mb-0">Stats</span>
                                    </a>
                                </div>
                                <!--end:Item-->
                            </div>
                            <!--end:Nav-->
                            
                        </div>
                        <!--end::Menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::Admin links-->    
                            <?php endif; ?>
                                
                    <!--begin::Chat-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3">
                        <!--begin::Menu wrapper-->
                        <div class="position-relative btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" id="kt_drawer_chat_toggle">
                            <i class="ki-duotone ki-message-text-2 fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                        </div>
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::Chat-->
                            
                    <!--begin::Theme mode-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3">
                        <!--begin::Menu toggle-->
                        <a href="#" class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-night-day theme-light-show fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                                <span class="path7"></span>
                                <span class="path8"></span>
                                <span class="path9"></span>
                                <span class="path10"></span>
                            </i>
                            <i class="ki-duotone ki-moon theme-dark-show fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                        <!--begin::Menu toggle-->
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-duotone ki-night-day fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                            <span class="path7"></span>
                                            <span class="path8"></span>
                                            <span class="path9"></span>
                                            <span class="path10"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Light</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-duotone ki-moon fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">Dark</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                    <span class="menu-icon" data-kt-element="icon">
                                        <i class="ki-duotone ki-screen fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                    <span class="menu-title">System</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Theme mode-->
                    <!--begin::User-->
                    <div class="d-flex align-items-center me-lg-n2 ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <!--begin::Menu wrapper-->
                    <?php if (isset($displayName)) : ?>
                        <span class="fw-bold text-white">Hi, <?php echo htmlspecialchars($displayName); ?>!</span>    
                        <?php else: ?>
                            <div class="d-flex align-items-center ms-1 ms-lg-3">
                            <button type="button" class="btn btn-info">Sign-up</button>
                            </div>
                        <?php endif; ?>
                        <div class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            <img class="h-30px w-30px rounded" src="assets/media/avatars/blank.png" alt="" />
                        </div>
                        <!--begin::User account menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    
        
                                <!--begin::Avatar-->
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Logo" src="assets/media/avatars/blank.png" />
                                    </div>
                                    <!--end::Avatar-->
                                
                        
                                <!--begin::Username-->
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold d-flex align-items-center fs-5">  
                                            <span class="badge badge-light-success fw-bold fs-8 py-1 ">
                                            <?php if (!empty($user_type) ) {
                                                echo $user_type;
                                            } ?>
                                            </span>
                                        </div>
                                            <div class="fw-bold d-flex align-items-center fs-5"><?php echo htmlspecialchars($displayName); ?></div>
                                    </div>
                                    <!--end::Username-->
                                </div>
                            </div>
                            <!--end::Menu item--    
                            <!--begin::Menu item-->
                            <div class="menu-item px-5 my-1">
                                <a href="/index.php?controller=User&action=profile" class="menu-link px-5">Account Settings</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-5">
                                <a href="/index.php?controller=Auth&action=logout" class="menu-link px-5">Sign Out</a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::User account menu-->
                        <!--end::Menu wrapper-->
                    </div>
                    <!--end::User -->
                    <!--begin::Aside mobile toggle-->
                    <!--end::Aside mobile toggle-->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Header-->
    <!--begin::Toolbar-->
    <div class="toolbar py-5 pb-lg-15" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column me-3">
                <!--begin::Title-->
                <h1 class="d-flex text-white fw-bold my-1 fs-3"><?php echo $title; ?></h1>
                <!--end::Title-->
                <?php if (!empty($breadcrumbs)) : ?>
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-white opacity-75">
                        <a href="/?controller=Project&action=index" class="text-white text-hover-primary">Home</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
        
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                    <li class="breadcrumb-item<?php echo $breadcrumb['active'] ? ' active' : ''; ?> text-white opacity-75"
                        <?php echo $breadcrumb['active'] ? 'aria-current="page"' : ''; ?>>
                        <?php if (!$breadcrumb['active']) : ?>
                        <a class="text-white" href="<?php echo $breadcrumb['url']; ?>"><?php echo htmlspecialchars($breadcrumb['label']); ?></a>
                    <?php else: ?>
                        <span class="text-white"><?php echo htmlspecialchars($breadcrumb['label']); ?></span>
                    <?php endif; ?>
                    </li>
                        <?php if ($index < count($breadcrumbs) - 1) : ?>
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                        <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <?php endif; ?>
                    <?php endforeach; ?>
            
                </ul>
                <!--end::Breadcrumb-->
                <?php endif; ?>
            </div>
            <!--end::Page title-->
        
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-3 py-md-1">
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-3 py-md-1">
             
            
            <?php if (!empty($headerNavbuttons)) : ?>
                    <?php foreach ($headerNavbuttons as $label => $button) : ?>
                        <!--begin::Button-->
                        <a href="<?php echo htmlspecialchars($button['url']); ?>" 
                                class="<?php if (!empty($button['class'])) :  echo htmlspecialchars($button['class']); 
                               endif; ?>" 
                                id="<?php echo !empty($button['id']) ? htmlspecialchars($button['id']) : ''; ?>"
                                <?php if (!empty($button['data'])) :
                                    foreach ($button['data'] as $key => $value) : 
                                        echo 'data-'.htmlspecialchars($key).'="'.htmlspecialchars($value).'" ';
                                    endforeach; 
                                endif; ?>
                            data-bs-theme="light">
                            <?php if (!empty($button['icon'])) : ?>
                                <i class="<?php echo htmlspecialchars($button['icon']); ?>"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                        <!--end::Button-->
                    <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <!--end::Actions-->            
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->


<?php else: ?>
<header class="py-3 text-center">
    <a href="/" class="logo">
        <img src="dist/img/testflow-logo.png"
        alt="Logo">
    </a>
</header>
<?php endif; ?>
