<?php
/**
 * API: Configurações
 */

require_once __DIR__ . '/../controllers/SettingsController.php';

$ctrl   = new SettingsController();
$action = $_GET['action'] ?? '';

match ($action) {
    'store'       => $ctrl->store(),
    'update_logo' => $ctrl->updateLogo(),
    default       => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
