<?php
require_once __DIR__ . '/../models/QuoteModel.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/ServiceModel.php';
require_once __DIR__ . '/../models/InvoiceModel.php';

class QuoteController {
    private QuoteModel $model;

    public function __construct() {
        requireAuth();
        $this->model = new QuoteModel();
    }

    public function index() {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $data = $this->model->getList($page, 15, $search, $status);
        extract($data); // $items, $total, $page, $last_page

        require __DIR__ . '/../views/quotes/index.php';
    }

    public function create() {
        $clientModel = new ClientModel();
        $serviceModel = new ServiceModel();
        
        $clients = $clientModel->getActive();
        $services = $serviceModel->getActive();
        
        $companySettings = Database::getInstance()
            ->query("SELECT * FROM company_settings LIMIT 1")->fetch();

        require __DIR__ . '/../views/quotes/create.php';
    }

    public function show(int $id) {
        $quote = $this->model->getWithDetails($id);
        if (!$quote) {
            setFlash('Orçamento não encontrado.', 'error');
            header('Location: /Sistema%20de%20Faturacao/orcamentos.php');
            exit;
        }
        require __DIR__ . '/../views/quotes/show.php';
    }

    public function store() {
        error_log("QuoteController::store called");
        verifyCsrf();
        error_log("CSRF verified");
        
        try {
            error_log("POST data: " . print_r($_POST, true));
            $data = [
                'client_id'   => (int)$_POST['client_id'],
                'issue_date'  => $_POST['issue_date'],
                'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                'status'      => $_POST['status'] ?? 'enviado',
                'subtotal'    => (float)$_POST['subtotal'],
                'iva_amount'  => (float)$_POST['iva_amount'],
                'discount'    => (float)($_POST['discount'] ?? 0),
                'total'       => (float)$_POST['total'],
                'notes'       => $_POST['notes'] ?? '',
                'terms'       => $_POST['terms'] ?? ''
            ];

            // Extract items (handle both indexed and non-indexed arrays from FormData)
            $items = [];
            $descriptions = $_POST['item_description'] ?? [];
            foreach ($descriptions as $i => $desc) {
                if (empty(trim($desc))) continue;
                $items[] = [
                    'service_id'  => !empty($_POST['item_service_id'][$i]) ? (int)$_POST['item_service_id'][$i] : null,
                    'description' => trim($desc),
                    'quantity'    => isset($_POST['item_quantity'][$i]) ? (float)$_POST['item_quantity'][$i] : 1,
                    'unit_price'  => isset($_POST['item_price'][$i]) ? (float)$_POST['item_price'][$i] : 0,
                    'total'       => isset($_POST['item_total'][$i]) ? (float)$_POST['item_total'][$i] : 0
                ];
            }

            if (empty($items)) {
                jsonResponse(false, 'Deve adicionar pelo menos um item.');
            }

            $quoteId = $this->model->create($data, $items);
            jsonResponse(true, 'Orçamento criado com sucesso!', ['id' => $quoteId]);

        } catch (Exception $e) {
            jsonResponse(false, 'Erro ao criar orçamento: ' . $e->getMessage());
        }
    }

    public function changeStatus(int $id) {
        verifyCsrf();
        $status = $_POST['status'] ?? '';
        
        if ($this->model->updateStatus($id, $status)) {
            jsonResponse(true, 'Estado atualizado com sucesso.');
        } else {
            jsonResponse(false, 'Estado inválido ou erro ao atualizar.');
        }
    }

    public function print(int $id) {
        $quote = $this->model->getWithDetails($id);
        if (!$quote) die('Orçamento não encontrado.');
        
        $company = Database::getInstance()
            ->query("SELECT * FROM company_settings LIMIT 1")->fetch();

        require __DIR__ . '/../views/quotes/print.php';
    }

    public function convertToInvoice(int $id) {
        verifyCsrf();
        
        try {
            $quote = $this->model->getWithDetails($id);
            if (!$quote) jsonResponse(false, 'Orçamento não encontrado.');
            if ($quote['status'] === 'convertido') jsonResponse(false, 'Este orçamento já foi convertido.');

            $invoiceModel = new InvoiceModel();
            
            // Get next invoice number
            $settings = Database::getInstance()
                ->query("SELECT invoice_prefix, next_invoice_number FROM company_settings LIMIT 1")->fetch();
            $nextNum = generateInvoiceNumber($settings['invoice_prefix'], $settings['next_invoice_number']);

            $invoiceData = [
                'client_id'      => $quote['client_id'],
                'quote_id'       => $quote['id'],
                'invoice_number' => $nextNum,
                'issue_date'     => date('Y-m-d'),
                'due_date'       => date('Y-m-d', strtotime('+30 days')),
                'status'         => 'emitida',
                'iva_rate'       => 16.00,
                'discount'       => $quote['discount'],
                'notes'          => $quote['notes'],
                'terms'          => $quote['terms'],
                'created_by'     => $_SESSION['user_id']
            ];

            // Transform quote items to invoice items format
            $invoiceItems = [];
            foreach ($quote['items'] as $item) {
                $invoiceItems[] = [
                    'service_id'  => $item['service_id'],
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price']
                ];
            }

            $invoiceId = $invoiceModel->create($invoiceData, $invoiceItems);
            
            // Mark quote as converted
            $this->model->updateStatus($id, 'convertido');
            Database::getInstance()->query("UPDATE quotes SET converted_to_invoice_id = ? WHERE id = ?", [$invoiceId, $id]);

            jsonResponse(true, 'Orçamento convertido com sucesso!', ['invoice_id' => $invoiceId]);

        } catch (Exception $e) {
            jsonResponse(false, 'Erro ao converter: ' . $e->getMessage());
        }
    }
}
