<?php
/**
 * Entry point: Configurações da Empresa
 */
require_once __DIR__ . '/controllers/SettingsController.php';
$ctrl = new SettingsController();
$ctrl->index();
