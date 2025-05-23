<?php

require_once __DIR__ . '/BaseController.php';

class UserController extends BaseController
{
    public function __construct($pdo)
    {
        parent::__construct($pdo);
    
        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login&error=Please+login+first');
            exit;
        }
    
        // Só exige admin em todas as ações exceto estas:
        $publicActions = ['profile', 'updateProfile'];
        $currentAction = $_GET['action'] ?? '';
    
        if (!in_array($currentAction, $publicActions) && empty($_SESSION['is_admin'])) {
            header('Location: /index.php?controller=Auth&action=login&error=Admin+access+required');
            exit;
        }
    }
    
    public function index()
    {
        $stmt = $this->pdo->query("
            SELECT * FROM moderators
            ORDER BY created_at DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            ['label' => __('User Management'), 'url' => '', 'active' => true],
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
        $isSuperadmin = !empty($_POST['is_superadmin']) ? 1 : 0;
        $password = $_POST['password'] ?? null;
        $user_type = $_POST['user_type'] ?? null;
        $is_confirmed = '1';

        if (!$email || !$password) {
            echo "Email and password are required.";
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO moderators (username, email, fullname, company, password_hash, is_admin, is_superadmin, is_confirmed)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $email,
            $fullname,
            $company,
            $passwordHash,
            $isAdmin,
            $isSuperadmin,
            $user_type,
            $is_confirmed
        ]);

        header('Location: /index.php?controller=User&action=index');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT * FROM moderators WHERE id = ?");
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

    $id       = (int)$_POST['id'];
    $email    = $_POST['email'];
    $fullname = $_POST['fullname'] ?? null;
    $company  = $_POST['company']  ?? null;
    $isAdmin  = !empty($_POST['is_admin']) ? 1 : 0;
    $isSuperadmin = !empty($_POST['is_superadmin']) ? 1 : 0;
    $user_type = $_POST['user_type'] ?? null;
    $is_confirmed = '1';
    $password = $_POST['password'] ?? null;

    // --- build the base query ---
    $sql  = "UPDATE moderators
             SET email = ?, username = ?, fullname = ?, company = ?, is_admin = ?, is_superadmin = ?, user_type = ?, is_confirmed = ?, updated_at = NOW()";

    $params = [$email, $email, $fullname, $company, $isAdmin, $isSuperadmin, $user_type, $is_confirmed];

    // --- add password if supplied ---
    if (!empty($password)) {
        $sql     .= ", password_hash = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

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
            ['label' => __('Profile'), 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/users/profile.php';
    }

    public function updateProfile()
    {
        $id = $_SESSION['user_id'];
        $fullname = $_POST['fullname'] ?? null;
        $company = $_POST['company'] ?? null;
        $email = $_POST['email'] ?? null;
        $is_admin = !empty($_POST['is_admin']) ? 1 : 0;
        $is_superadmin = !empty($_POST['is_superadmin']) ? 1 : 0;
        $user_type = $_POST['user_type'] ?? null;
        $password = $_POST['new_password'] ?? null;

        $stmt = $this->pdo->prepare("
            UPDATE moderators
            SET email = ?, username = ?, fullname = ?, company = ?, is_admin = ?, is_superadmin = ?, user_type = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $email,
            $email,
            $fullname,
            $company,
            $is_admin,
            $is_superadmin,
            $user_type,
            $user_type,
            $id
        ]);

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
