<?php
/**
 * Controller: Clientes
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/InvoiceModel.php';


class ClientController
{
    private ClientModel $model;

    public function __construct()
    {
        requireAuth();
        $this->model = new ClientModel();
    }

    /** Listar clientes */
    public function index(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $data = $this->model->getList($page, 15, $search, $status);
        extract($data); // $items, $total, $page, $per_page, $last_page
        require_once __DIR__ . '/../views/clients/index.php';
    }

    /** Detalhes do cliente */
    public function show(int $id): void
    {
        $client = $this->model->findById($id);
        if (!$client) {
            setFlash('error', 'Cliente não encontrado.');
            redirect('/Sistema%20de%20Faturacao/clientes.php');
        }
        $stats    = $this->model->getStats($id);
        $invoices = (new \InvoiceModel())->getList(1, 10, '', '', $id)['items'];
        require_once __DIR__ . '/../views/clients/show.php';
    }

    /** Criar cliente (POST) */
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
        jsonResponse(true, 'Cliente criado com sucesso!', ['id' => $id]);
    }

    /** Actualizar cliente (POST) */
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
        jsonResponse(true, 'Cliente actualizado com sucesso!');
    }

    /** Eliminar cliente */
    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            jsonResponse(false, 'Sem permissão para eliminar clientes.', [], 403);
        }
        try {
            $this->model->delete($id);
            jsonResponse(true, 'Cliente eliminado com sucesso!');
        } catch (\PDOException $e) {
            jsonResponse(false, 'Não é possível eliminar: o cliente tem faturas associadas.', [], 409);
        }
    }

    /** Validação */
    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) {
            $errors[] = 'O nome é obrigatório.';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        return $errors;
    }
}
