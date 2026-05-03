<?php
/**
 * API: Perfil
 */

require_once __DIR__ . '/../controllers/ProfileController.php';

$ctrl   = new ProfileController();
$action = $_GET['action'] ?? '';

match ($action) {
    'update'          => $ctrl->update(),
    'change_password' => $ctrl->changePassword(),
    'update_avatar'   => $ctrl->updateAvatar(),
    default           => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
