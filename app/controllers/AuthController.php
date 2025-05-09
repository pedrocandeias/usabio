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
            if (!$user['is_confirmed']) {
                header('Location: /index.php?controller=Auth&action=login&error=confirm_required');
                exit;
            }
        
            // OK: continuar com login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'] ?? $user['username'];
            $_SESSION['user_type'] = $user['user_type'] ?? 'normal';
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_superadmin'] = $user['is_superadmin'];
        
            // Track login
            $stmt = $this->pdo->prepare("
                UPDATE moderators SET last_login = NOW(), last_login_ip = ?, last_login_user_agent = ? 
                WHERE id = ?
            ");
            $stmt->execute([$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $user['id']]);
        
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
    
    public function storeRegistration()
    {
        require_once __DIR__ . '/../helpers/mailhelper.php';
    
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $toc = $_POST['toc'] ?? '';
    
        // Validação
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
    
        // Gera username único
        $baseUsername = strtolower(strstr($email, '@', true));
        $username = $baseUsername;
        $counter = 1;
    
        $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE username = ?");
        while (true) {
            $stmt->execute([$username]);
            if (!$stmt->fetch()) break;
            $username = $baseUsername . '-' . $counter++;
        }
    
        // Verifica duplicação de email
        $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header('Location: index.php?controller=Auth&action=register&error=exists_email');
            exit;
        }
    
        // Cria utilizador
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO moderators (username, email, password_hash, fullname, company, is_admin) 
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->execute([$username, $email, $hash, $fullname, $company]);
    
        $newUserId = $this->pdo->lastInsertId();
    
        // Gera token de confirmação
        $token = bin2hex(random_bytes(32));
        $stmt = $this->pdo->prepare("
            UPDATE moderators SET confirmation_token = ?, is_confirmed = 0 WHERE id = ?
        ");
        $stmt->execute([$token, $newUserId]);
    
        // Cria link de confirmação
        $baseUrl = MailHelper::getLoginUrl($this->pdo); // usa o mesmo base
        $confirmUrl = str_replace('login', 'confirmEmail&token=' . $token, $baseUrl);
    
        // Envia email de confirmação
        MailHelper::sendConfirmationEmail($email, $fullname, $confirmUrl, $this->pdo);
    
        // Verifica convites pendentes
        $stmt = $this->pdo->prepare("SELECT * FROM pending_invite_emails WHERE email = ?");
        $stmt->execute([$email]);
        $pendingInvites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!empty($pendingInvites)) {
            require_once __DIR__ . '/../models/ProjectInvite.php';
            $inviteModel = new ProjectInvite($this->pdo);
    
            foreach ($pendingInvites as $invite) {
                $inviteModel->createInvite($invite['project_id'], $newUserId);
                $stmt = $this->pdo->prepare("UPDATE pending_invite_emails SET status = 'registered' WHERE id = ?");
                $stmt->execute([$invite['id']]);
            }
        }
    
        header('Location: index.php?controller=Auth&action=login&success=confirm_required');
        exit;
    }
    

    public function confirmEmail()
    {
        $token = $_GET['token'] ?? '';
    
        if (!$token || strlen($token) < 10) {
            $message = "Invalid confirmation link.";
            include __DIR__ . '/../views/auth/confirmation_result.php';
            return;
        }
    
        // Verifica token
        $stmt = $this->pdo->prepare("SELECT id FROM moderators WHERE confirmation_token = ?");
        $stmt->execute([$token]);
        $userId = $stmt->fetchColumn();
    
        if (!$userId) {
            $message = "Invalid or expired confirmation token.";
            include __DIR__ . '/../views/auth/confirmation_result.php';
            return;
        }
    
        // Atualiza utilizador
        $stmt = $this->pdo->prepare("
            UPDATE moderators SET is_confirmed = 1, confirmation_token = NULL WHERE id = ?
        ");
        $stmt->execute([$userId]);
    
        $message = "✅ Your email has been confirmed. You may now log in.";
        include __DIR__ . '/../views/auth/confirmation_result.php';
    }
    


}
