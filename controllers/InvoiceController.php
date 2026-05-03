<?php
/**
 * Controller: Faturas
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../includes/EmailService.php';

class InvoiceController
{
    private InvoiceModel  $model;
    private ClientModel   $clientModel;
    private ServiceModel  $serviceModel;

    public function __construct()
    {
        requireAuth();
        $this->model        = new InvoiceModel();
        $this->clientModel  = new ClientModel();
        $this->serviceModel = new ServiceModel();
    }

    /** Listar faturas */
    public function index(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $data = $this->model->getList($page, 15, $search, $status);
        extract($data);
        require_once __DIR__ . '/../views/invoices/index.php';
    }

    /** Formulário de criação */
    public function create(): void
    {
        $clients  = $this->clientModel->getActive();
        $services = $this->serviceModel->getActive();

        // Próximo número de fatura
        $settings = Database::getInstance()->query(
            "SELECT `invoice_prefix`, `next_invoice_number`, `iva_rate` FROM `company_settings` WHERE `id` = 1"
        )->fetch();

        $nextInvoiceNumber = generateInvoiceNumber(
            $settings['invoice_prefix'] ?? 'FAT',
            (int) ($settings['next_invoice_number'] ?? 1)
        );
        $ivaRate = (float) ($settings['iva_rate'] ?? DEFAULT_IVA_RATE);

        require_once __DIR__ . '/../views/invoices/create.php';
    }

    /** Detalhes da fatura */
    public function show(int $id): void
    {
        $invoice = $this->model->findWithDetails($id);
        if (!$invoice) {
            setFlash('error', 'Fatura não encontrada.');
            redirect('/Sistema%20de%20Faturacao/faturas.php');
        }

        $company = Database::getInstance()->query(
            "SELECT * FROM `company_settings` WHERE `id` = 1"
        )->fetch();

        require_once __DIR__ . '/../views/invoices/show.php';
    }

    /** Guardar nova fatura */
    public function store(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        // Validação
        $errors = [];
        if (empty($_POST['client_id'])) $errors[] = 'Seleccione um cliente.';
        if (empty($_POST['due_date']))   $errors[] = 'A data de vencimento é obrigatória.';
        if (empty($_POST['items']) || !is_array($_POST['items'])) {
            $errors[] = 'A fatura deve ter pelo menos um item.';
        }

        if ($errors) {
            jsonResponse(false, implode(' ', $errors), ['errors' => $errors], 422);
        }

        // Gerar número de fatura
        $settings = Database::getInstance()->query(
            "SELECT `invoice_prefix`, `next_invoice_number` FROM `company_settings` WHERE `id` = 1"
        )->fetch();

        $_POST['invoice_number'] = generateInvoiceNumber(
            $settings['invoice_prefix'] ?? 'FAT',
            (int) $settings['next_invoice_number']
        );
        $_POST['issue_date']  = $_POST['issue_date'] ?? date('Y-m-d');
        $_POST['created_by']  = $_SESSION['user_id'];

        try {
            $id = $this->model->create($_POST, $_POST['items']);
            jsonResponse(true, 'Fatura criada com sucesso!', ['id' => $id, 'invoice_number' => $_POST['invoice_number']]);
        } catch (\Throwable $e) {
            error_log('Erro ao criar fatura: ' . $e->getMessage());
            jsonResponse(false, 'Erro ao criar fatura. Tente novamente.', [], 500);
        }
    }

    /** Alterar estado da fatura */
    public function changeStatus(int $id): void
    {
        $status  = sanitize($_POST['status'] ?? '');
        $allowed = ['rascunho', 'emitida', 'paga', 'vencida', 'cancelada'];

        if (!in_array($status, $allowed)) {
            jsonResponse(false, 'Estado inválido.', [], 422);
        }

        $this->model->updateStatus($id, $status);
        jsonResponse(true, 'Estado actualizado com sucesso!');
    }

    /** Visualizar fatura para impressão/PDF */
    public function print(int $id): void
    {
        $invoice = $this->model->findWithDetails($id);
        if (!$invoice) {
            http_response_code(404);
            die('Fatura não encontrada.');
        }
        $company = Database::getInstance()->query(
            "SELECT * FROM `company_settings` WHERE `id` = 1"
        )->fetch();
        require_once __DIR__ . '/../views/invoices/print.php';
    }

    /** Enviar fatura por email */
    public function sendEmail(int $id): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $invoice = $this->model->findWithDetails($id);
        if (!$invoice) {
            jsonResponse(false, 'Fatura não encontrada.', [], 404);
        }

        if (empty($invoice['client_email'])) {
            jsonResponse(false, 'Este cliente não tem um endereço de email registado.', [], 422);
        }

        $company = Database::getInstance()->query(
            "SELECT * FROM `company_settings` WHERE `id` = 1"
        )->fetch();

        try {
            $sent = EmailService::sendInvoice($invoice, $company);
            if ($sent) {
                jsonResponse(true, 'Fatura enviada com sucesso para ' . $invoice['client_email']);
            } else {
                jsonResponse(false, 'Falha ao enviar email. Verifique a configuração do servidor.');
            }
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao enviar email: ' . $e->getMessage());
        }
    }
}
