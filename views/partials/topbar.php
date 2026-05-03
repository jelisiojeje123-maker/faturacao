<?php
/**
 * Layout parcial: Topbar / Header
 * $pageTitle deve ser definida na view
 */
$pageTitle = $pageTitle ?? 'Dashboard';
$base = '/faturacao';
$flash = getFlash();
?>
<header class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-40 flex items-center justify-between px-6 py-3 w-full">
    <!-- Mobile menu toggle + Page title -->
    <div class="flex items-center gap-4">
        <button id="sidebar-toggle" class="md:hidden text-slate-600 p-1">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div>
            <h2 class="text-lg font-bold text-slate-900 leading-tight"><?= htmlspecialchars($pageTitle) ?></h2>
        </div>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-3">
        <!-- Search -->
        <div class="hidden lg:flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 w-72">
            <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2">search</span>
            <input type="text" id="global-search" placeholder="Pesquisar…" class="bg-transparent border-none focus:ring-0 text-sm w-full text-slate-700 placeholder:text-slate-400 outline-none">
        </div>
        <!-- New invoice shortcut -->
        <a href="<?= $base ?>/criar-fatura.php"
           class="hidden sm:flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nova Fatura
        </a>
        <!-- User avatar -->
        <a href="<?= $base ?>/perfil.php" class="flex items-center gap-2 pl-3 border-l border-slate-200 cursor-pointer group">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold overflow-hidden shadow-sm">
                <?php if (!empty($_SESSION['user_avatar'])): ?>
                    <img src="<?= $base ?>/assets/img/avatars/<?= $_SESSION['user_avatar'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= htmlspecialchars(getInitials($_SESSION['user_name'] ?? 'U')) ?>
                <?php endif; ?>
            </div>
            <span class="hidden sm:inline text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
        </a>
    </div>
</header>

<?php if ($flash): ?>
<div id="flash-msg"
     class="mx-6 mt-4 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-3
            <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
    <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
    <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100">✕</button>
</div>
<script>setTimeout(()=>{ const el = document.getElementById('flash-msg'); if(el) el.style.display='none'; }, 5000);</script>
<?php endif; ?>
