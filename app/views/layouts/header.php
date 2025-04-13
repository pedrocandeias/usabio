<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TestFlow: <?php echo $title; ?></title>
    <link
  rel="stylesheet"
  href="dist/css/styles.css" />

</head>

<body>

<?php if (empty($minimalLayout)): ?>
  <header class="p-3 text-bg-dark">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <!-- Logo / Branding -->
      
        <a class="navbar-brand d-flex align-items-center logo" href="/?controller=Project&action=index">
           <img
            src="dist/img/testflow-logo.png"
            alt="Logo"
          />
        </a>

        <!-- Mobile Toggle Button -->
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left-aligned menu items -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item">
              <a class="nav-link" href="/index.php?controller=Session&action=dashboard">Test Sessions</a>
          </li>
          <?php endif; ?>  
          
          <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="navbarDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >Projects</a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li>
                  <a class="dropdown-item" href="/?controller=Project&action=index">
                View All Projects
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/?controller=Project&action=create">
                Create New Project
                  </a>
                </li>
              </ul>
            </li>
          </ul>

        
        
          <!-- Right-aligned user info and logout -->
          <?php if (!empty($_SESSION['is_superadmin'])): ?>
            <div class="nav-item dropdown me-2">
                <a class="btn btn-outline-success text-white fw-bold dropdown-toggle" href="#" id="superadminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    ⚙️ Superadmin
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/index.php?controller=User&action=index">User Management</a></li>
                    <!-- <li><a class="dropdown-item" href="#">System Settings</a></li> -->
                </ul>
          </div>
         
            <?php endif; ?>

          <?php if (isset($_SESSION['username'])): ?>
          <?php $displayName = $_SESSION['fullname'] ?? $_SESSION['username']; ?>
          <div class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
            <span class="fw-bold">Hi, <?php echo htmlspecialchars($displayName); ?>!</span>
          </div>
          <div class="dropdown text-end">
            <a href="#" class="d-block link-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small">
              <li><a class="dropdown-item" href="#">Settings</a></li>
              <li><a class="dropdown-item" href="/index.php?controller=User&action=profile">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item btn btn-danger" href="/index.php?controller=Auth&action=logout">Sign out</a></li>
            </ul>
          </div>
          
            <?php else: ?>
            <div class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
              <button type="button" class="btn btn-info">Sign-up</button>
          </div>
            <?php endif; ?>
          
        </div>
      </div>
    </nav>
  </header>
  
    <?php if (!empty($breadcrumbs)): ?>
    <nav aria-label="breadcrumb" class="bg-light pt-3 pb-3">
      <div class="container">
        <ol class="breadcrumb breadcrumb-custom">
          <li class="breadcrumb-item">
            <a href="/?controller=Project&action=index">Home</a>
          </li>
            <?php foreach ($breadcrumbs as $breadcrumb): ?>
                <li class="breadcrumb-item<?php echo $breadcrumb['active'] ? ' active' : ''; ?>"
                    <?php echo $breadcrumb['active'] ? 'aria-current="page"' : ''; ?>>
                    <?php if (!$breadcrumb['active']): ?>
                        <a href="<?php echo $breadcrumb['url']; ?>"><?php echo htmlspecialchars($breadcrumb['label']); ?></a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($breadcrumb['label']); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
      </div>
    </nav>
<?php endif; ?>
<?php else: ?>
    <header class="py-3 text-center">
        <a href="/" class="logo">
            <img src="dist/img/testflow-logo.png"
            alt="Logo">
        </a>
    </header>
<?php endif; ?>
<main>
