<?php
/**
 * Entry Point: pagamentos.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/PaymentController.php';

(new PaymentController())->index();
