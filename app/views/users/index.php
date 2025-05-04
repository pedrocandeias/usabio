<?php 
$menuActive = 'overview';
$title = 'Project details - Overview';
$pageTitle = 'Project details - Overview';
$pageDescription = 'Manage your project and test sessions.';
$headerNavbuttons = [
    __('back_to_projects_list') => [
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

<?php require_once __DIR__ . '/../layouts/admin-header.php'; ?>

        <!--begin::Row-->
        <div class="row g-5 g-xl-8">

<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
        <div class="card-toolbar">
            <a href="/index.php?controller=User&action=create" class="btn btn-primary">Add User</a>
        </div>
    </div>
    <div class="card-body">
    
                <table class="table-striped table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                    <thead class="fs-7 text-gray-500 text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Admin</th>
                            <th>Superadmin</th>
                            <th>Projects</th>
                            <th>Last Login</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['company']); ?></td>
                                <td><?php echo $user['is_admin'] ? '✅' : ''; ?></td>
                                <td><?php echo $user['is_superadmin'] ? '✅' : ''; ?></td>
                                <td>
                                    <?php if (!empty($user['projects'])): ?>
                                        <ul class="mb-0 ps-3 small">
                                            <?php foreach ($user['projects'] as $project): ?>
                                                <li><?php echo htmlspecialchars($project); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user['last_login'] ?: '—'; ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($user['last_login_ip'] ?? ''); ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($user['last_login_user_agent'] ?? ''); ?></td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td><?php echo $user['updated_at']; ?></td>
                                <td class="text-nowrap">
                                    <a href="/index.php?controller=User&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <?php if ($_SESSION['is_superadmin'] ?? false): ?>
                                        <a href="/index.php?controller=User&action=destroy&id=<?php echo $user['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                                    </div> 
    </div>
    <!--end::Post-->
</div>
<!--end::Container-->
<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>