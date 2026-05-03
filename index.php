<?php
/**
 * Entry Point: index.php — Dashboard
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/DashboardController.php';

(new DashboardController())->index();
