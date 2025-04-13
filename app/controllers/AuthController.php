<?php

class AuthController
{
    private $pdo;

    /**
     * Constructor receives the PDO connection.
     * Starts session so we can track the logged-in user.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Show the login form.
     */
    public function login()
    {
        // If user is already logged in, redirect to the main page (or wherever).
        if (isset($_SESSION['username'])) {
            
            header('Location: /?controller=Project&action=index');
            exit;
        }

        // Optionally capture an error message in the URL, e.g., /?controller=Auth&action=login&error=Invalid
        $error = $_GET['error'] ?? null;

        // Load the login view (make sure the path is correct for your app)
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process the POST from the login form.
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $this->pdo->prepare("SELECT * FROM moderators WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // ✅ Store session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'] ?? $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_superadmin'] = $user['is_superadmin']; // fix here too

            $stmt = $this->pdo->prepare(
                "
            UPDATE moderators
            SET last_login = NOW(),
                last_login_ip = ?,
                last_login_user_agent = ?
            WHERE id = ?
        "
            );
            $stmt->execute(
                [
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT'],
                $user['id']
                ]
            );

            // ✅ Redirect after login
            header('Location: /?controller=Project&action=index');
            exit;
        }

        header('Location: /?controller=Auth&action=login&error=Invalid%20credentials');
        exit;
    }


    /**
     * Logout the user by destroying the session.
     */
    public function logout()
    {
        session_destroy();
        header('Location: /?controller=Auth&action=login');
        exit;
    }
}
