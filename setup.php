<?php
/**
 * FaturaMZ Pro — Script de Configuração Inicial
 * Executar UMA VEZ após importar o schema.sql
 * URL: http://localhost/Sistema%20de%20Faturacao/setup.php
 *
 * APAGAR este ficheiro após a configuração!
 */

// Configuração da BD (copiar de config/app.php)
$host    = 'localhost';
$dbName  = 'faturacao_mz';
$dbUser  = 'root';
$dbPass  = '';
$charset = 'utf8mb4';

$steps   = [];
$success = true;

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbName};charset={$charset}",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $steps[] = ['ok', '✅ Ligação à base de dados <strong>faturacao_mz</strong> estabelecida com sucesso.'];
} catch (PDOException $e) {
    $steps[] = ['err', '❌ Erro de ligação: ' . htmlspecialchars($e->getMessage())];
    $success = false;
}

if ($success) {
    // 1. Verificar tabelas
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $required = ['users','clients','services','invoices','invoice_items','payments','company_settings'];
    $missing  = array_diff($required, $tables);

    if ($missing) {
        $steps[] = ['err', '❌ Tabelas em falta: <code>' . implode(', ', $missing) . '</code>. Importe o <strong>schema.sql</strong> primeiro.'];
        $success = false;
    } else {
        $steps[] = ['ok', '✅ Todas as ' . count($required) . ' tabelas encontradas.'];
    }

    // 2. Criar/actualizar utilizador admin
    if ($success) {
        $adminEmail    = 'admin@empresa.mz';
        $adminPassword = 'admin123';
        $adminHash     = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 10]);

        // Verificar se já existe
        $existing = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $existing->execute([$adminEmail]);
        $user = $existing->fetch();

        if ($user) {
            // Actualizar hash
            $pdo->prepare("UPDATE users SET password = ?, is_active = 1 WHERE email = ?")
                ->execute([$adminHash, $adminEmail]);
            $steps[] = ['ok', "✅ Senha do utilizador <strong>{$adminEmail}</strong> redefinida para <strong>{$adminPassword}</strong>."];
        } else {
            // Criar
            $pdo->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, 'admin', 1)")
                ->execute(['Administrador', $adminEmail, $adminHash]);
            $steps[] = ['ok', "✅ Utilizador admin criado: <strong>{$adminEmail}</strong> / <strong>{$adminPassword}</strong>."];
        }

        // 3. Criar operador de teste
        $opEmail    = 'operador@empresa.mz';
        $opPassword = 'operador123';
        $opHash     = password_hash($opPassword, PASSWORD_BCRYPT, ['cost' => 10]);

        $existingOp = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $existingOp->execute([$opEmail]);

        if ($existingOp->fetch()) {
            $pdo->prepare("UPDATE users SET password = ?, is_active = 1 WHERE email = ?")
                ->execute([$opHash, $opEmail]);
            $steps[] = ['ok', "✅ Senha do operador <strong>{$opEmail}</strong> redefinida para <strong>{$opPassword}</strong>."];
        } else {
            $pdo->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, 'operador', 1)")
                ->execute(['Operador Teste', $opEmail, $opHash]);
            $steps[] = ['ok', "✅ Operador criado: <strong>{$opEmail}</strong> / <strong>{$opPassword}</strong>."];
        }

        // 4. Verificar/inserir configuração da empresa
        $companyCount = (int)$pdo->query("SELECT COUNT(*) FROM company_settings")->fetchColumn();
        if ($companyCount === 0) {
            $pdo->exec("INSERT INTO company_settings (name, nuit, email, phone, address, city, iva_rate, currency, invoice_prefix, next_invoice_number, payment_terms, bank_name, bank_account)
                        VALUES ('Minha Empresa, Lda.','400 000 001','geral@empresa.mz','+258 21 000 000','Av. Principal, 1','Maputo',16.00,'MT','FAT',1,'Pagamento a 30 dias.','Millennium BIM','000000000')");
            $steps[] = ['ok', '✅ Configuração da empresa inserida.'];
        } else {
            $steps[] = ['info', 'ℹ️ Configuração da empresa já existe — não foi alterada.'];
        }

        // 5. Verificar dados de exemplo (clientes)
        $clientCount = (int)$pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
        $steps[] = ['info', "ℹ️ Clientes na base de dados: <strong>{$clientCount}</strong>." .
            ($clientCount === 0 ? ' <em>Importe o seed.sql para dados de exemplo.</em>' : '')];

        // 6. Testar password_verify
        $verifyTest = password_verify($adminPassword, $adminHash);
        if ($verifyTest) {
            $steps[] = ['ok', '✅ <code>password_verify()</code> a funcionar correctamente — PHP ' . PHP_VERSION];
        } else {
            $steps[] = ['err', '❌ Erro crítico: <code>password_verify()</code> falhou. Verifique a versão do PHP.'];
            $success = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FaturaMZ Pro — Configuração Inicial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-xl mb-4">
                <span style="font-size:2rem;">🧾</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">FaturaMZ Pro</h1>
            <p class="text-slate-500 text-sm mt-1">Script de Configuração Inicial</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-3 mb-6">
            <?php foreach ($steps as [$type, $msg]): ?>
            <div class="flex items-start gap-3 p-3 rounded-xl text-sm
                <?= match($type) {
                    'ok'   => 'bg-emerald-50 text-emerald-800',
                    'err'  => 'bg-rose-50 text-rose-800',
                    'info' => 'bg-blue-50 text-blue-800',
                    default => 'bg-slate-50 text-slate-700',
                } ?>">
                <div class="flex-1"><?= $msg ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($success): ?>
        <!-- Credentials summary -->
        <div class="bg-slate-900 text-white rounded-2xl p-6 mb-6">
            <h2 class="font-black text-lg mb-4">🔑 Credenciais de Acesso</h2>
            <div class="space-y-3">
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Administrador</p>
                    <p class="text-sm">📧 Email: <code class="text-blue-300 font-bold">admin@empresa.mz</code></p>
                    <p class="text-sm">🔐 Senha: <code class="text-emerald-300 font-bold">admin123</code></p>
                </div>
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Operador</p>
                    <p class="text-sm">📧 Email: <code class="text-blue-300 font-bold">operador@empresa.mz</code></p>
                    <p class="text-sm">🔐 Senha: <code class="text-emerald-300 font-bold">operador123</code></p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="/Sistema%20de%20Faturacao/login.php"
               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-all">
                🚀 Entrar no Sistema
            </a>
        </div>

        <div class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm text-center">
            ⚠️ <strong>Segurança:</strong> Apague este ficheiro (<code>setup.php</code>) após a configuração!
        </div>
        <?php else: ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm">
            ❌ <strong>Configuração incompleta.</strong> Corrija os erros acima e recarregue a página.
        </div>
        <?php endif; ?>

        <p class="text-center text-xs text-slate-400 mt-6">
            PHP <?= PHP_VERSION ?> &bull; MySQL &bull; FaturaMZ Pro v1.0
        </p>
    </div>
</body>
</html>
