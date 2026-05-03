<?php
/**
 * Entry point: Gestão de Utilizadores
 */
require_once __DIR__ . '/controllers/UserController.php';
$ctrl = new UserController();
$ctrl->index();
