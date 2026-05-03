<?php
/**
 * Configuração da Base de Dados
 * Sistema de Faturação - Moçambique
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'faturacao_mz');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da aplicação
define('APP_NAME', 'FaturaMZ Pro');
define('APP_URL', 'http://localhost/Sistema%20de%20Faturacao');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'Africa/Maputo');

// Segurança
define('CSRF_TOKEN_NAME', '_csrf_token');
define('SESSION_LIFETIME', 7200); // 2 horas

// Pasta de uploads
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', APP_URL . '/assets/uploads/');

// IVA padrão para Moçambique
define('DEFAULT_IVA_RATE', 16.0);
define('DEFAULT_CURRENCY', 'MT');
define('DEFAULT_CURRENCY_SYMBOL', 'MT');

// Configurar timezone
date_default_timezone_set(APP_TIMEZONE);

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}
