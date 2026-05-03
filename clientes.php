<?php
/**
 * Entry Point: clientes.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/InvoiceModel.php'; // necessário para show()
require_once __DIR__ . '/controllers/ClientController.php';

$controller = new ClientController();

// Ver detalhes de um cliente
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $controller->show((int)$_GET['id']);
} else {
    $controller->index();
}
