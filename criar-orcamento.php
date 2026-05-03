<?php
/**
 * Entry Point: criar-orcamento.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/QuoteController.php';

(new QuoteController())->create();
