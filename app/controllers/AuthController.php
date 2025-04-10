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
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process the POST from the login form.
     */
    public function processLogin()
    {
        // We only handle POST requests here. If not POST, redirect back to login form.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        // Grab the form data
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Find the user in the "moderators" table
        $stmt = $this->pdo->prepare("SELECT * FROM moderators WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password against the stored hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // If valid, store the user info in the session
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin']; // or any additional fields you need

            // Redirect to the main page (Project index, for example)
            header('Location: /?controller=Project&action=index');
            exit;
        }

        // Otherwise, credentials are invalid, so redirect back with an error message
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
