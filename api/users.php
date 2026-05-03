<?php
/**
 * API: Utilizadores
 */

require_once __DIR__ . '/../controllers/UserController.php';

$ctrl   = new UserController();
$action = $_GET['action'] ?? '';
$id     = (int) ($_GET['id'] ?? 0);

match ($action) {
    'get'           => $ctrl->get($id),
    'store'         => $ctrl->store(),
    'update'        => $ctrl->update($id),
    'delete'        => $ctrl->destroy($id),
    'toggle_status' => $ctrl->toggleStatus($id),
    default         => jsonResponse(false, 'Acção desconhecida.', [], 400),
};
