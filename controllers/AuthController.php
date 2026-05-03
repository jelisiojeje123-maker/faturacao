<?php
/**
 * Controller: Auth (Login / Logout)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /** Mostrar formulário de login */
    public function showLogin(): void
    {
        if (isAuthenticated()) {
            redirect('/faturacao/index.php');
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /** Processar login */
    public function login(): void
    {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            setFlash('error', 'Token de segurança inválido. Tente novamente.');
            redirect('/faturacao/login.php');
        }

        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validação básica
        if (empty($email) || empty($password)) {
            setFlash('error', 'Email e senha são obrigatórios.');
            redirect('/faturacao/login.php');
        }

        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            setFlash('error', 'Credenciais inválidas. Verifique o email e a senha.');
            redirect('/faturacao/login.php');
        }

        // Regenerar sessão para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['logged_in_at'] = time();

        setFlash('success', 'Bem-vindo(a), ' . $user['name'] . '!');
        redirect('/faturacao/index.php');
    }

    /** Logout */
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('/faturacao/login.php');
    }
}
