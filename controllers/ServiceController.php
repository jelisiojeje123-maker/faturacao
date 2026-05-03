<?php
/**
 * Controller: Serviços
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/ServiceModel.php';

class ServiceController
{
    private ServiceModel $model;

    public function __construct()
    {
        requireAuth();
        $this->model = new ServiceModel();
    }

    /** Listar serviços */
    public function index(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['search'] ?? '');
        $type   = sanitize($_GET['type'] ?? '');

        $data = $this->model->getList($page, 15, $search, $type);
        extract($data);
        require_once __DIR__ . '/../views/services/index.php';
    }

    /** Criar serviço (AJAX) */
    public function store(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $errors = $this->validate($_POST);
        if ($errors) {
            jsonResponse(false, implode(' ', $errors), ['errors' => $errors], 422);
        }

        $id = $this->model->create($_POST);
        $service = $this->model->findById($id);
        jsonResponse(true, 'Serviço criado com sucesso!', ['service' => $service]);
    }

    /** Actualizar serviço (AJAX) */
    public function update(int $id): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $errors = $this->validate($_POST);
        if ($errors) {
            jsonResponse(false, implode(' ', $errors), ['errors' => $errors], 422);
        }

        $this->model->update($id, $_POST);
        jsonResponse(true, 'Serviço actualizado com sucesso!');
    }

    /** Eliminar serviço */
    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            jsonResponse(false, 'Sem permissão.', [], 403);
        }
        try {
            $this->model->delete($id);
            jsonResponse(true, 'Serviço eliminado.');
        } catch (\PDOException $e) {
            jsonResponse(false, 'Serviço em uso em faturas — não é possível eliminar.', [], 409);
        }
    }

    /** Obter serviço por ID (AJAX) */
    public function get(int $id): void
    {
        $service = $this->model->findById($id);
        if (!$service) {
            jsonResponse(false, 'Serviço não encontrado.', [], 404);
        }
        jsonResponse(true, '', ['service' => $service]);
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) $errors[] = 'O nome é obrigatório.';
        if (!isset($data['price']) || (float)$data['price'] < 0) $errors[] = 'O preço deve ser igual ou superior a zero.';
        return $errors;
    }
}
