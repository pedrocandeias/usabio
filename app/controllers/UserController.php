<?php

class UserController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['username']) || !$_SESSION['is_admin']) {
            header('Location: /index.php?controller=Auth&action=login&error=Admin+access+required');
            exit;
        }

        $this->pdo = $pdo;
    }

    public function index()
{
    $stmt = $this->pdo->query("
        SELECT * FROM moderators
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch project assignments per user
    foreach ($users as &$user) {
        $stmt = $this->pdo->prepare("
            SELECT p.title
            FROM project_user pu
            JOIN projects p ON p.id = pu.project_id
            WHERE pu.moderator_id = ?
            ORDER BY p.title
        ");
        $stmt->execute([$user['id']]);
        $user['projects'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    $breadcrumbs = [
        ['label' => 'User Management', 'url' => '', 'active' => true],
    ];

    include __DIR__ . '/../views/users/index.php';
}

    public function create()
    {
        $user = ['id' => 0, 'username' => '', 'company' => '', 'is_admin' => 0];
        require __DIR__ . '/../views/users/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=User&action=index');
            exit;
        }

        $email = $_POST['email'];
        $fullname = $_POST['fullname'] ?? null;
        $company = $_POST['company'] ?? null;
        $isAdmin = !empty($_POST['is_admin']) ? 1 : 0;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            echo "Email and password are required.";
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO moderators (username, email, fullname, company, password_hash, is_admin)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $email, // username = email
            $email,
            $fullname,
            $company,
            $passwordHash,
            $isAdmin
        ]);

        header('Location: /index.php?controller=User&action=index');
        exit;
    }


    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT id, username, is_admin FROM moderators WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "User not found.";
            exit;
        }

        require __DIR__ . '/../views/users/form.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=User&action=index');
            exit;
        }

        $id = $_POST['id'];
        $email = $_POST['email'];
        $fullname = $_POST['fullname'] ?? null;
        $company = $_POST['company'] ?? null;
        $isAdmin = !empty($_POST['is_admin']) ? 1 : 0;

        $stmt = $this->pdo->prepare("
            UPDATE moderators
            SET email = ?, username = ?, fullname = ?, company = ?, is_admin = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $email, // also updates username
            $email,
            $fullname,
            $company,
            $isAdmin,
            $id
        ]);

        header('Location: /index.php?controller=User&action=index');
        exit;
    }

    public function profile()
    {
        $id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT * FROM moderators WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Profile', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/users/profile.php';
    }

    public function updateProfile()
{
    $id = $_SESSION['user_id'];
    $fullname = $_POST['fullname'] ?? null;
    $company = $_POST['company'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['new_password'] ?? null;

    // Update base fields
    $stmt = $this->pdo->prepare("
        UPDATE moderators
        SET email = ?, username = ?, fullname = ?, company = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $email,
        $email, // username = email
        $fullname,
        $company,
        $id
    ]);

    // Update password if provided
    if (!empty($password)) {
        $stmt = $this->pdo->prepare("UPDATE moderators SET password_hash = ? WHERE id = ?");
        $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }

    header("Location: /index.php?controller=User&action=profile&success=1");
    exit;
}



    public function destroy()
    {
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM moderators WHERE id = ?");
            $stmt->execute([$id]);
        }

        header('Location: /index.php?controller=User&action=index');
        exit;
    }
}
