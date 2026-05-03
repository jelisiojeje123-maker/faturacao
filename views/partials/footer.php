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
// SweetAlert2 Toast configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

function showToast(message, type = 'success') {
    Toast.fire({
        icon: type,
        title: message
    });
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
// Confirmar eliminação com SweetAlert2
function confirmDelete(message, onConfirm) {
    Swal.fire({
        title: 'Tem a certeza?',
        text: message || 'Esta acção não poderá ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl font-bold px-6 py-3',
            cancelButton: 'rounded-xl font-bold px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
}

// Formatar valor em MT
function formatMT(value) {
    return 'MT ' + parseFloat(value || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
