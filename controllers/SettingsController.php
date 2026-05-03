<?php
/**
 * Controller: Configurações
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

class SettingsController
{
    private $db;

    public function __construct()
    {
        requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            setFlash('error', 'Acesso negado. Apenas administradores podem aceder às configurações.');
            redirect('/Sistema%20de%20Faturacao/index.php');
        }
        $this->db = Database::getInstance();
    }

    /** Exibir formulário de configurações */
    public function index(): void
    {
        $settings = $this->db->query("SELECT * FROM `company_settings` WHERE `id` = 1")->fetch();
        require_once __DIR__ . '/../views/settings/index.php';
    }

    /** Guardar configurações */
    public function store(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        $data = [
            'name'                => sanitize($_POST['name']),
            'nuit'                => sanitize($_POST['nuit']),
            'email'               => sanitize($_POST['email']),
            'phone'               => sanitize($_POST['phone']),
            'address'             => sanitize($_POST['address']),
            'city'                => sanitize($_POST['city']),
            'iva_rate'            => (float) ($_POST['iva_rate'] ?? 16),
            'currency'            => sanitize($_POST['currency'] ?? 'MT'),
            'invoice_prefix'      => sanitize($_POST['invoice_prefix'] ?? 'FAT'),
            'next_invoice_number' => (int) ($_POST['next_invoice_number'] ?? 1),
            'payment_terms'       => sanitize($_POST['payment_terms']),
            'bank_name'           => sanitize($_POST['bank_name']),
            'bank_account'        => sanitize($_POST['bank_account']),
        ];

        try {
            $sql = "UPDATE `company_settings` SET 
                    `name` = ?, `nuit` = ?, `email` = ?, `phone` = ?, `address` = ?, `city` = ?, 
                    `iva_rate` = ?, `currency` = ?, `invoice_prefix` = ?, `next_invoice_number` = ?, 
                    `payment_terms` = ?, `bank_name` = ?, `bank_account` = ? 
                    WHERE `id` = 1";
            
            $this->db->query($sql, array_values($data));
            jsonResponse(true, 'Configurações actualizadas com sucesso!');
        } catch (\Throwable $e) {
            error_log('Erro ao guardar configurações: ' . $e->getMessage());
            jsonResponse(false, 'Erro ao guardar configurações.', [], 500);
        }
    }

    /** Actualizar logotipo */
    public function updateLogo(): void
    {
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            jsonResponse(false, 'Token CSRF inválido.', [], 403);
        }

        if (empty($_FILES['logo'])) {
            jsonResponse(false, 'Nenhum ficheiro enviado.');
        }

        $filename = handleUpload($_FILES['logo'], __DIR__ . '/../assets/img/logo/');
        if (!$filename) {
            jsonResponse(false, 'Erro ao processar imagem.');
        }

        try {
            // Apagar logo antigo se existir
            $old = $this->db->query("SELECT `logo` FROM `company_settings` WHERE `id` = 1")->fetchColumn();
            if ($old && file_exists(__DIR__ . '/../assets/img/logo/' . $old)) {
                unlink(__DIR__ . '/../assets/img/logo/' . $old);
            }

            $this->db->query("UPDATE `company_settings` SET `logo` = ? WHERE `id` = 1", [$filename]);
            jsonResponse(true, 'Logotipo actualizado!', ['filename' => $filename]);
        } catch (\Throwable $e) {
            jsonResponse(false, 'Erro ao guardar na base de dados.');
        }
    }
}
