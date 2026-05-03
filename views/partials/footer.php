<?php
/**
 * Layout partial: Toast container + Mobile sidebar overlay
 */
?>
<!-- Toast container -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-2 pointer-events-none"></div>

<!-- Mobile sidebar overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden modal-backdrop"
     onclick="closeMobileSidebar()"></div>

<!-- Mobile sidebar clone wrapper -->
<div id="mobile-sidebar" class="fixed left-0 top-0 h-screen w-[260px] z-50 bg-[#1E293B] transform -translate-x-full transition-transform duration-300 md:hidden overflow-y-auto">
    <?php include __DIR__ . '/sidebar.php'; ?>
</div>

<script>
/* =========================================================
   Utilitários globais JS
   ========================================================= */

// Toast
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const colors = {
        success: 'bg-emerald-500',
        error:   'bg-rose-500',
        info:    'bg-blue-500',
        warning: 'bg-amber-500',
    };
    const icons = {
        success: 'check_circle',
        error:   'error',
        info:    'info',
        warning: 'warning',
    };
    const toast = document.createElement('div');
    var typeClass = colors[type] ? colors[type] : colors.info;
    toast.className = 'toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl text-white text-sm font-semibold shadow-lg ' + typeClass + ' min-w-[280px] max-w-[380px]';
    toast.innerHTML = `
        <span class="material-symbols-outlined text-[20px]">${icons[type]}</span>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="opacity-70 hover:opacity-100 ml-2">✕</button>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// CSRF token para requisições AJAX
const metaCsrf = document.querySelector('meta[name="csrf-token"]');
const CSRF_TOKEN = metaCsrf ? metaCsrf.content : '';

// Axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Mobile sidebar
function openMobileSidebar() {
    document.getElementById('mobile-sidebar').classList.remove('-translate-x-full');
    document.getElementById('mobile-overlay').classList.remove('hidden');
}
function closeMobileSidebar() {
    document.getElementById('mobile-sidebar').classList.add('-translate-x-full');
    document.getElementById('mobile-overlay').classList.add('hidden');
}
document.getElementById('sidebar-toggle')?.addEventListener('click', openMobileSidebar);

// Confirmar eliminação
function confirmDelete(message, onConfirm) {
    var msg = message ? message : 'Tem a certeza que deseja eliminar este registo?';
    if (confirm(msg)) {
        onConfirm();
    }
}

// Formatar valor em MT
function formatMT(value) {
    return 'MT ' + parseFloat(value || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
