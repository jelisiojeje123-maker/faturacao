<?php
/**
 * Entry point: Impressão de Recibo
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/PaymentModel.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);
$paymentModel = new PaymentModel();

$payment = Database::getInstance()->query(
    "SELECT p.*, i.invoice_number, i.issue_date as invoice_date, i.total as invoice_total,
            c.name as client_name, c.nuit as client_nuit, c.address as client_address,
            u.name as received_by
     FROM payments p
     JOIN invoices i ON i.id = p.invoice_id
     JOIN clients c ON c.id = i.client_id
     JOIN users u ON u.id = p.created_by
     WHERE p.id = ? LIMIT 1",
    [$id]
)->fetch();

if (!$payment) die('Pagamento não encontrado.');

$company = Database::getInstance()->query("SELECT * FROM company_settings LIMIT 1")->fetch();

require_once __DIR__ . '/views/payments/print.php';
