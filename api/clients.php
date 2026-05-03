<?php
/**
 * API: Clientes — endpoints AJAX
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../controllers/ClientController.php';

// Apenas requisições AJAX
header('Content-Type: application/json; charset=utf-8');

if (!isAuthenticated()) {
    jsonResponse(false, 'Não autorizado.', [], 401);
}

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$ctrl   = new ClientController();

match ($action) {
    'store'  => $ctrl->store(),
    'update' => $ctrl->update($id),
    'delete' => $ctrl->destroy($id),
    default  => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
