<?php
/**
 * API: Faturas — endpoints AJAX
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../controllers/InvoiceController.php';

header('Content-Type: application/json; charset=utf-8');

if (!isAuthenticated()) {
    jsonResponse(false, 'Não autorizado.', [], 401);
}

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$ctrl   = new InvoiceController();

match ($action) {
    'store'        => $ctrl->store(),
    'change_status'=> $ctrl->changeStatus($id),
    'send_email'   => $ctrl->sendEmail($id),
    'print'        => $ctrl->print($id),
    default        => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
