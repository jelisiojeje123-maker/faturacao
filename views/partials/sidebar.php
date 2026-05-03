<?php
/**
 * Layout parcial: Sidebar de navegação
 * Incluir em todas as views autenticadas
 * $currentPage deve ser definida na view (ex: 'dashboard', 'clients', etc.)
 */
$currentPage = $currentPage ?? '';

function navLink(string $page, string $href, string $icon, string $label, string $current): string {
    $active = ($current === $page)
        ? 'bg-blue-600 text-white'
        : 'text-slate-400 hover:text-white hover:bg-slate-800';
    return <<<HTML
    <a href="{$href}"
       class="{$active} rounded-lg mx-2 my-0.5 flex items-center px-4 py-3 transition-all duration-200 group">
        <span class="material-symbols-outlined mr-3 text-[20px]">{$icon}</span>
        <span class="font-medium text-sm tracking-tight">{$label}</span>
    </a>
    HTML;
}

$base = '/faturacao';
?>
<aside class="bg-[#1E293B] fixed left-0 top-0 h-screen w-[260px] border-r border-slate-700/50 shadow-2xl flex flex-col py-6 z-50 hidden md:flex">
    <!-- Logo -->
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/30">
            <span class="material-symbols-outlined text-white text-xl">receipt_long</span>
        </div>
        <div>
            <h1 class="text-base font-black text-white leading-tight">FaturaMZ Pro</h1>
            <p class="text-[10px] text-slate-400 tracking-wide uppercase">Sistema de Faturação</p>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto space-y-0.5 px-2">
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest px-4 pb-2">Principal</p>
        <?= navLink('dashboard',  $base.'/index.php',     'dashboard',    'Dashboard',    $currentPage) ?>
        <?= navLink('clients',    $base.'/clientes.php',  'group',        'Clientes',     $currentPage) ?>
        <?= navLink('services',   $base.'/servicos.php',  'build_circle', 'Serviços',     $currentPage) ?>

        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest px-4 pt-4 pb-2">Faturação</p>
        <?= navLink('quotes',     $base.'/orcamentos.php',    'request_quote', 'Orçamentos',   $currentPage) ?>
        <?= navLink('create_quote',$base.'/criar-orcamento.php','post_add',    'Criar Orçamento',$currentPage) ?>
        <?= navLink('invoices',   $base.'/faturas.php',       'description',  'Faturas',      $currentPage) ?>
        <?= navLink('create_inv', $base.'/criar-fatura.php',  'add_circle',   'Criar Fatura', $currentPage) ?>
        <?= navLink('payments',   $base.'/pagamentos.php',    'payments',     'Pagamentos',   $currentPage) ?>

        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest px-4 pt-4 pb-2">Análise</p>
        <?= navLink('reports',    $base.'/relatorios.php','bar_chart',    'Relatórios',   $currentPage) ?>

        <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest px-4 pt-4 pb-2">Sistema</p>
        <?= navLink('users',      $base.'/utilizadores.php','people',     'Utilizadores',  $currentPage) ?>
        <?= navLink('settings',   $base.'/configuracoes.php','settings',   'Configurações', $currentPage) ?>
        <?php endif; ?>
    </nav>

    <!-- User info & Logout -->
    <div class="px-4 mt-4 border-t border-slate-700/50 pt-4">
        <a href="<?= $base ?>/perfil.php" class="flex items-center gap-3 px-2 mb-3 group/user hover:bg-slate-800/50 p-2 rounded-xl transition-all">
            <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold shadow group-hover/user:scale-110 transition-transform overflow-hidden">
                <?php if (!empty($_SESSION['user_avatar'])): ?>
                    <img src="<?= $base ?>/assets/img/avatars/<?= $_SESSION['user_avatar'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= htmlspecialchars(getInitials($_SESSION['user_name'] ?? 'U')) ?>
                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate group-hover/user:text-blue-400 transition-colors"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
                <p class="text-slate-400 text-xs truncate capitalize"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></p>
            </div>
        </a>
        <a href="<?= $base ?>/logout.php"
           class="w-full flex items-center gap-2 px-4 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all text-sm">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span>Terminar Sessão</span>
        </a>
    </div>
</aside>
