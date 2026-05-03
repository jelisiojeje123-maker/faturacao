<?php
/**
 * Controller: Perfil do Utilizador
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class ProfileController
{
    private UserModel $model;

    public function __construct()
    {
        requireAuth();
        $this->model = new UserModel();
    }

    /** Exibir perfil */
    public function index(): void
    {
        $user = $this->model->findById($_SESSION['user_id']);
        require_once __DIR__ . '/../views/profile/index.php';
    }

    /** Actualizar dados básicos */
    public function update(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $data = [
            'name'  => sanitize($_POST['name']),
            'email' => strtolower(trim($_POST['email'])),
        ];

        if (empty($data['name']) || empty($data['email'])) {
            jsonResponse(false, 'Nome e email são obrigatórios.', [], 422);
        }

        try {
            $this->model->update($_SESSION['user_id'], $data);
            $_SESSION['user_name'] = $data['name'];
            jsonResponse(true, 'Perfil actualizado com sucesso!');
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao actualizar perfil.', [], 500);
        }
    }

    /** Alterar senha */
    public function changePassword(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6) {
            jsonResponse(false, 'A nova senha deve ter pelo menos 6 caracteres.', [], 422);
        }
        if ($new !== $confirm) {
            jsonResponse(false, 'As senhas não coincidem.', [], 422);
        }

        $user = $this->model->findById($_SESSION['user_id']);
        if (!password_verify($current, $user['password'])) {
            jsonResponse(false, 'A senha actual está incorrecta.', [], 422);
        }

        try {
            $this->model->changePassword($_SESSION['user_id'], $new);
            jsonResponse(true, 'Senha alterada com sucesso!');
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao alterar senha.', [], 500);
        }
    }

    /** Actualizar avatar */
    public function updateAvatar(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        if (empty($_FILES['avatar'])) {
            jsonResponse(false, 'Nenhum ficheiro enviado.');
        }

        $filename = handleUpload($_FILES['avatar'], __DIR__ . '/../assets/img/avatars/');
        if (!$filename) {
            jsonResponse(false, 'Erro ao processar imagem.');
        }

        try {
            // Apagar avatar antigo se existir
            $user = $this->model->findById($_SESSION['user_id']);
            if ($user['avatar'] && file_exists(__DIR__ . '/../assets/img/avatars/' . $user['avatar'])) {
                unlink(__DIR__ . '/../assets/img/avatars/' . $user['avatar']);
            }

            $this->model->update($_SESSION['user_id'], ['avatar' => $filename]);
            $_SESSION['user_avatar'] = $filename;
            jsonResponse(true, 'Avatar actualizado!', ['filename' => $filename]);
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao guardar na base de dados.');
        }
    }
}
