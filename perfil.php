<?php
/**
 * Entry point: Perfil do Utilizador
 */
require_once __DIR__ . '/controllers/ProfileController.php';
$ctrl = new ProfileController();
$ctrl->index();
