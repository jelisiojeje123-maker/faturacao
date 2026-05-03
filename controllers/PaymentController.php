<?php
/**
 * Controller: Pagamentos
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/InvoiceModel.php';

class PaymentController
{
    private PaymentModel  $model;
    private InvoiceModel  $invoiceModel;

    public function __construct()
    {
        requireAuth();
        $this->model        = new PaymentModel();
        $this->invoiceModel = new InvoiceModel();
    }

    /** Listar pagamentos */
    public function index(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['search'] ?? '');
        $method = sanitize($_GET['method'] ?? '');

        $data = $this->model->getList($page, 15, $search, $method);
        extract($data);
        require_once __DIR__ . '/../views/payments/index.php';
    }

    /** Registar pagamento (POST/AJAX) */
    public function store(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $amount    = (float)($_POST['amount'] ?? 0);

        // Validações
        $errors = [];
        if (!$invoiceId) $errors[] = 'Fatura não especificada.';
        if ($amount <= 0) $errors[] = 'O valor deve ser superior a zero.';
        if (empty($_POST['payment_date'])) $errors[] = 'A data de pagamento é obrigatória.';
        if (empty($_POST['method'])) $errors[] = 'O método de pagamento é obrigatório.';

        if ($errors) {
            jsonResponse(false, implode(' ', $errors), ['errors' => $errors], 422);
        }

        // Verificar se a fatura existe e o valor não excede o saldo
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            jsonResponse(false, 'Fatura não encontrada.', [], 404);
        }
        if ($amount > (float)$invoice['amount_due'] + 0.01) {
            jsonResponse(false, 'O valor excede o saldo em dívida da fatura.', [], 422);
        }

        $_POST['created_by'] = $_SESSION['user_id'];

        try {
            $paymentId = $this->model->register($_POST);
            // Recalcular totais e estado da fatura
            $this->invoiceModel->recalculateAmounts($invoiceId);

            $payment = $this->model->findById($paymentId);
            jsonResponse(true, 'Pagamento registado com sucesso!', [
                'payment_id'     => $paymentId,
                'receipt_number' => $payment['receipt_number'],
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao registar pagamento: ' . $e->getMessage());
            jsonResponse(false, 'Erro ao registar pagamento.', [], 500);
        }
    }

    /** Eliminar pagamento */
    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            jsonResponse(false, 'Sem permissão.', [], 403);
        }
        $payment = $this->model->findById($id);
        if (!$payment) {
            jsonResponse(false, 'Pagamento não encontrado.', [], 404);
        }
        $invoiceId = (int)$payment['invoice_id'];
        $this->model->delete($id);
        $this->invoiceModel->recalculateAmounts($invoiceId);
        jsonResponse(true, 'Pagamento eliminado.');
    }
}
