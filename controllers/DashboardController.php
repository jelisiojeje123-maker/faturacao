<?php
/**
 * Controller: Dashboard
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/ClientModel.php';

class DashboardController
{
    private InvoiceModel $invoiceModel;
    private ClientModel  $clientModel;

    public function __construct()
    {
        requireAuth();
        // Auto-marcar faturas vencidas
        $this->invoiceModel = new InvoiceModel();
        $this->clientModel  = new ClientModel();
        $this->invoiceModel->markOverdue();
    }

    /** Página principal do dashboard */
    public function index(): void
    {
        $kpis        = $this->invoiceModel->getDashboardKpis();
        $chartData   = $this->invoiceModel->getMonthlyChart();
        $dueSoon     = $this->invoiceModel->getDueSoon(7);
        $recentInvoices = $this->invoiceModel->getList(1, 6)['items'];
        $totalClients   = $this->clientModel->count();
        
        $pendingQuotes  = Database::getInstance()->query("SELECT COUNT(*) FROM quotes WHERE status = 'enviado'")->fetchColumn();

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
