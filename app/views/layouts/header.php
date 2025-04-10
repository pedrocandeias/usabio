<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TestFlow: <?php echo $title; ?></title>
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
/>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <!-- Logo / Branding -->
  
    <a class="navbar-brand d-flex align-items-center" href="/?controller=Project&action=index">
      <!-- Replace with your actual logo path -->
      <img
        src="../assets/img/testflow-logo.png"
        alt="Logo"
        style="height: 40px; width: auto; margin-right: 8px;"
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
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            Projects
          </a>
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
       
        <?php if (!empty($_SESSION['is_admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/index.php?controller=User&action=index">User Management</a>
            </li>
        <?php endif; ?>
        <!-- Add more nav items if needed -->
      </ul>

      <!-- Right-aligned user info and logout -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
    <?php if (isset($_SESSION['username'])): ?>
        <li class="nav-item d-flex align-items-center me-2">
            <span class="nav-link disabled">
                Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </span>
        </li>

        <li class="nav-item">
            <a class="btn btn-outline-danger ms-2" href="/index.php?controller=Auth&action=logout">Logout</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="btn btn-success" href="/index.php?controller=Auth&action=login">Login</a>
        </li>
    <?php endif; ?>
</ul>

    </div>
  </div>
</nav>
