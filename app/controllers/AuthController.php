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
    
        // ✅ Só verifica após confirmação do utilizador
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'] ?? $user['username'];
            $_SESSION['user_type'] = $user['user_type'] ?? 'normal';
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_superadmin'] = $user['is_superadmin'];
    
            // Track login data
            $stmt = $this->pdo->prepare("
                UPDATE moderators
                SET last_login = NOW(),
                    last_login_ip = ?,
                    last_login_user_agent = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT'],
                $user['id']
            ]);
    
      
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


    public function register() {
        include __DIR__ . '/../views/auth/register.php';
    }
    
    public function storeRegistration() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $toc = $_POST['toc'] ?? '';
    
        // Validação básica dos campos
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?controller=Auth&action=register&error=invalid_email');
            exit;
        }
    
        if (!$password || $password !== $confirm) {
            header('Location: index.php?controller=Auth&action=register&error=password_mismatch');
            exit;
        }
    
        if (!$toc) {
            header('Location: index.php?controller=Auth&action=register&error=terms');
            exit;
        }
    
        // Gera username automático a partir do email
        $baseUsername = strtolower(strstr($email, '@', true));
        $username = $baseUsername;
        $counter = 1;
    
        // Verifica se username já existe, se sim, acrescenta número incremental
        $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE username = ?");
        while (true) {
            $stmt->execute([$username]);
            if (!$stmt->fetch()) {
                break;  // Username livre
            }
            $username = $baseUsername . '-' . $counter++;
        }
    
        // Verifica duplicação de email
        $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header('Location: index.php?controller=Auth&action=register&error=exists_email');
            exit;
        }
    
        // Insere novo moderador na base de dados
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO moderators (username, email, password_hash, is_admin) VALUES (?, ?, ?, 0)");
        $stmt->execute([$username, $email, $hash]);

        $newUserId = $this->pdo->lastInsertId();

        // Verifica se havia convites pendentes para este email
        $stmt = $this->pdo->prepare("
            SELECT * FROM pending_invite_emails WHERE email = ?
        ");
        $stmt->execute([$email]);
        $pendingInvites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($pendingInvites)) {
            require_once __DIR__ . '/../models/ProjectInvite.php';
            $inviteModel = new ProjectInvite($this->pdo);
        
            foreach ($pendingInvites as $invite) {
                // Cria convite real
                $inviteModel->createInvite($invite['project_id'], $newUserId);
        
                // Marca como "registered"
                $stmt = $this->pdo->prepare("
                    UPDATE pending_invite_emails SET status = 'registered' WHERE id = ?
                ");
                $stmt->execute([$invite['id']]);
            }
        }
        

        header('Location: index.php?controller=Auth&action=login&success=registered');
        exit;
    }
    
}
