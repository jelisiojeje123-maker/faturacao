<?php
/**
 * Entry Point: faturas.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/InvoiceController.php';

$controller = new InvoiceController();

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'print' && $id) {
    $controller->print($id);
} elseif ($id) {
    $controller->show($id);
} else {
    $controller->index();
}
