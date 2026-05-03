<?php
/**
 * Entry Point: criar-fatura.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/InvoiceController.php';

(new InvoiceController())->create();
