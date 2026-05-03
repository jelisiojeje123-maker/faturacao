<?php
/**
 * API: Orçamentos — endpoints AJAX
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../controllers/QuoteController.php';

header('Content-Type: application/json; charset=utf-8');
error_log("Quotes API hit: action=" . ($_GET['action'] ?? 'none'));

if (!isAuthenticated()) {
    jsonResponse(false, 'Não autorizado.', [], 401);
}

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$ctrl   = new QuoteController();

match ($action) {
    'store'        => $ctrl->store(),
    'change_status'=> $ctrl->changeStatus($id),
    'convert'      => $ctrl->convertToInvoice($id),
    'print'        => $ctrl->print($id),
    default        => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
