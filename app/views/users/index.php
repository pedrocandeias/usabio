<?php $title = 'User Management'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">User Management</h1>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
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
        <tbody>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>
</body>
</html>