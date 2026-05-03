<?php
/**
 * Helpers globais do sistema
 * Funções utilitárias reutilizáveis
 */

/**
 * Formatar valor monetário em Meticais
 */
function formatMoney(float $amount, string $currency = 'MT'): string
{
    return $currency . ' ' . number_format($amount, 2, ',', '.');
}

/**
 * Formatar data para exibição (pt-MZ)
 */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    if (empty($date) || $date === '0000-00-00') return '—';
    return date($format, strtotime($date));
}

/**
 * Gerar token CSRF
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verificar token CSRF
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION[CSRF_TOKEN_NAME])
        && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Atalho para verificar CSRF e retornar erro JSON
 */
function verifyCsrf(): void
{
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        jsonResponse(false, 'Token CSRF inválido.', [], 403);
    }
}

/**
 * Input CSRF oculto para formulários
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
}

/**
 * Sanitizar string de input
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirecionar
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Verificar se o utilizador está autenticado
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verificar se é admin
 */
function isAdmin(): bool
{
    return isAuthenticated() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Exigir autenticação (redirecionar se não autenticado)
 */
function requireAuth(): void
{
    if (!isAuthenticated()) {
        redirect('/faturacao/login');
    }
}

/**
 * Exigir perfil admin
 */
function requireAdmin(): void
{
    requireAuth();
    if (!isAdmin()) {
        redirect('/faturacao/index?error=acesso_negado');
    }
}

/**
 * Resposta JSON padronizada para API
 */
function jsonResponse(bool $success, string $message, array $data = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Mensagem Flash (sessão)
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Obter e limpar mensagem Flash
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Gerar número de fatura sequencial
 */
function generateInvoiceNumber(string $prefix, int $nextNumber): string
{
    return $prefix . '-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Calcular IVA
 */
function calculateIva(float $subtotal, float $ivaRate = 16.0): float
{
    return round($subtotal * ($ivaRate / 100), 2);
}

/**
 * Obter classe CSS do estado da fatura
 */
function invoiceStatusClass(string $status): string
{
    return match($status) {
        'paga'      => 'bg-emerald-100 text-emerald-700',
        'emitida'   => 'bg-blue-100 text-blue-700',
        'vencida'   => 'bg-rose-100 text-rose-700',
        'rascunho'  => 'bg-slate-100 text-slate-600',
        'cancelada' => 'bg-gray-100 text-gray-500',
        default     => 'bg-gray-100 text-gray-600',
    };
}

/**
 * Obter label do estado da fatura
 */
function invoiceStatusLabel(string $status): string
{
    return match($status) {
        'paga'      => 'Paga',
        'emitida'   => 'Emitida',
        'vencida'   => 'Vencida',
        'rascunho'  => 'Rascunho',
        'cancelada' => 'Cancelada',
        default     => ucfirst($status),
    };
}

/**
 * Obter label do método de pagamento
 */
function paymentMethodLabel(string $method): string
{
    return match($method) {
        'dinheiro'      => 'Dinheiro',
        'transferencia' => 'Transferência Bancária',
        'mobile_money'  => 'Mobile Money (M-Pesa/e-Mola)',
        'cheque'        => 'Cheque',
        'outro'         => 'Outro',
        default         => ucfirst($method),
    };
}

/**
 * Truncar texto
 */
function truncate(string $text, int $length = 50): string
{
    return mb_strlen($text) > $length
        ? mb_substr($text, 0, $length) . '...'
        : $text;
}

/**
 * Iniciais do nome (para avatar)
 */
function getInitials(string $name): string
{
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    return $initials;
}

/**
 * Lidar com upload de ficheiros
 */
function handleUpload(array $file, string $destinationDir, array $allowedExts = ['jpg', 'jpeg', 'png', 'webp']): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) return null;

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $target   = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return null;
}
