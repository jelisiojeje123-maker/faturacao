<?php
/**
 * Entry Point: logout.php
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

(new AuthController())->logout();
