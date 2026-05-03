<?php
/**
 * Controller: Utilizadores
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private UserModel $model;

    public function __construct()
    {
        requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            setFlash('error', 'Acesso negado.');
            redirect('/faturacao/index.php');
        }
        $this->model = new UserModel();
    }

    /** Listar utilizadores */
    public function index(): void
    {
        $users = $this->model->findAll('name ASC');
        require_once __DIR__ . '/../views/users/index.php';
    }

    /** Obter utilizador por ID (AJAX) */
    public function get(int $id): void
    {
        $user = $this->model->findById($id);
        if (!$user) {
            jsonResponse(false, 'Utilizador não encontrado.', [], 404);
        }
        unset($user['password']); // Segurança
        jsonResponse(true, '', ['user' => $user]);
    }

    /** Criar utilizador */
    public function store(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $errors = [];
        if (empty($_POST['name']))  $errors[] = 'O nome é obrigatório.';
        if (empty($_POST['email'])) $errors[] = 'O email é obrigatório.';
        if (empty($_POST['password'])) $errors[] = 'A senha é obrigatória.';
        
        if ($errors) {
            jsonResponse(false, implode(' ', $errors), [], 422);
        }

        try {
            $this->model->create($_POST);
            jsonResponse(true, 'Utilizador criado com sucesso!');
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                jsonResponse(false, 'Este email já está registado.', [], 422);
            }
            jsonResponse(false, 'Erro ao criar utilizador.', [], 500);
        }
    }

    /** Actualizar utilizador (POST/AJAX) */
    public function update(int $id): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $errors = [];
        if (empty($_POST['name']))  $errors[] = 'O nome é obrigatório.';
        if (empty($_POST['email'])) $errors[] = 'O email é obrigatório.';
        
        if ($errors) {
            jsonResponse(false, implode(' ', $errors), [], 422);
        }

        $data = [
            'name'  => sanitize($_POST['name']),
            'email' => strtolower(trim($_POST['email'])),
            'role'  => $_POST['role'] ?? 'operador',
        ];

        // Se a senha for enviada, atualiza-a
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                jsonResponse(false, 'A senha deve ter pelo menos 6 caracteres.', [], 422);
            }
            $this->model->changePassword($id, $_POST['password']);
        }

        try {
            $this->model->update($id, $data);
            jsonResponse(true, 'Utilizador actualizado com sucesso!');
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao actualizar utilizador.', [], 500);
        }
    }

    /** Mudar estado (activar/desactivar) */
    public function toggleStatus(int $id): void
    {
        if ($id === (int)$_SESSION['user_id']) {
            jsonResponse(false, 'Não pode desactivar a sua própria conta.', [], 422);
        }

        $user = $this->model->findById($id);
        if (!$user) jsonResponse(false, 'Utilizador não encontrado.');

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->model->update($id, ['is_active' => $newStatus]);

        jsonResponse(true, 'Estado actualizado!');
    }

    /** Eliminar utilizador */
    public function destroy(int $id): void
    {
        if ($id === (int)$_SESSION['user_id']) {
            jsonResponse(false, 'Não pode eliminar a sua própria conta.', [], 422);
        }

        if (!isAdmin()) {
            jsonResponse(false, 'Sem permissão.', [], 403);
        }

        try {
            $this->model->delete($id);
            jsonResponse(true, 'Utilizador eliminado com sucesso!');
        } catch (\PDOException $e) {
            jsonResponse(false, 'Não é possível eliminar: o utilizador tem registos associados (faturas, pagamentos, etc).', [], 409);
        }
    }
}
