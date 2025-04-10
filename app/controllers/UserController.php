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
        $stmt = $this->pdo->query("SELECT id, username, is_admin FROM moderators ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/users/index.php';
    }

    public function create()
    {
        $user = ['id' => 0, 'username' => '', 'is_admin' => 0];
        require __DIR__ . '/../views/users/form.php';
    }

    public function store()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        if ($username && $password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO moderators (username, password_hash, is_admin) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $is_admin]);
        }

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
        $id = $_POST['id'] ?? 0;
        $username = $_POST['username'] ?? '';
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        if ($id && $username) {
            $stmt = $this->pdo->prepare("UPDATE moderators SET username = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$username, $is_admin, $id]);
        }

        if (!empty($_POST['password'])) {
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE moderators SET password_hash = ? WHERE id = ?");
            $stmt->execute([$passwordHash, $id]);
        }

        header('Location: /index.php?controller=User&action=index');
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
