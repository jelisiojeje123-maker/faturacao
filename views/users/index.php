<?php
/**
 * View: Listagem de Utilizadores
 */
$pageTitle   = 'Utilizadores';
$currentPage = 'users';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1200px] w-full mx-auto space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Gestão de Utilizadores</h1>
                    <p class="text-slate-500 text-sm mt-1">Administre as contas de acesso ao sistema</p>
                </div>
                <button onclick="resetUserForm(); openModal('user-modal')"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-blue-600/20 text-sm">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Novo Utilizador
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                                <th class="px-6 py-4 text-left">Utilizador</th>
                                <th class="px-6 py-4 text-left">Email</th>
                                <th class="px-6 py-4 text-center">Perfil</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acções</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shadow-sm">
                                            <?= getInitials($u['name']) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 leading-none"><?= htmlspecialchars($u['name']) ?></p>
                                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">ID: #<?= $u['id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wide
                                        <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                        <?= $u['role'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="toggleStatus(<?= $u['id'] ?>)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold transition-all
                                            <?= $u['is_active'] ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $u['is_active'] ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                                        <?= $u['is_active'] ? 'Activo' : 'Inactivo' ?>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="editUser(<?= $u['id'] ?>)"
                                                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="Editar Utilizador">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                        <button onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')"
                                                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all"
                                                title="Eliminar Utilizador">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add User Modal -->
<div id="user-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 modal-container">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Novo Utilizador</h3>
            <button onclick="closeModal('user-modal')" class="text-slate-400 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="user-form" class="p-6 space-y-4">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
            <input type="hidden" id="user-id" value="">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nome Completo *</label>
                <input type="text" name="name" required placeholder="Ex: João Sitoe"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email *</label>
                <input type="email" name="email" required placeholder="email@empresa.mz"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Senha de Acesso</label>
                <input type="password" name="password" id="user-password" minlength="6" placeholder="Deixe em branco para manter a actual"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Perfil / Permissões *</label>
                <select name="role" required class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                    <option value="operador">Operador (Faturação e Clientes)</option>
                    <option value="admin">Administrador (Acesso Total)</option>
                </select>
            </div>

            <div id="user-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('user-modal')"
                        class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all active:scale-95 shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span id="btn-label">Criar Conta</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/Sistema%20de%20Faturacao';

function openModal(id) {
    const m = document.getElementById(id);
    m.classList.replace('hidden', 'flex');
    setTimeout(() => {
        m.querySelector('.modal-container').classList.replace('scale-95', 'scale-100');
        m.querySelector('.modal-container').classList.replace('opacity-0', 'opacity-100');
    }, 10);
}

function closeModal(id) {
    const m = document.getElementById(id);
    m.querySelector('.modal-container').classList.replace('scale-100', 'scale-95');
    m.querySelector('.modal-container').classList.replace('opacity-100', 'opacity-0');
    setTimeout(() => m.classList.replace('flex', 'hidden'), 200);
}

document.getElementById('user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id  = document.getElementById('user-id').value;
    const btn = this.querySelector('button[type="submit"]');
    const err = document.getElementById('user-error');
    err.classList.add('hidden');
    btn.disabled = true;

    try {
        const fd = new FormData(this);
        const url = id ? `${BASE}/api/users.php?action=update&id=${id}` : `${BASE}/api/users.php?action=store`;
        const res = await axios.post(url, fd);
        if (res.data.success) {
            showToast(res.data.message);
            closeModal('user-modal');
            setTimeout(() => location.reload(), 800);
        } else {
            err.textContent = res.data.message;
            err.classList.remove('hidden');
        }
    } catch (e) {
        err.textContent = 'Erro de comunicação.';
        err.classList.remove('hidden');
    } finally {
        btn.disabled = false;
    }
});

function resetUserForm() {
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('svc-modal-title').textContent = 'Novo Utilizador';
    document.getElementById('btn-label').textContent = 'Criar Conta';
    document.getElementById('user-password').required = true;
    document.getElementById('user-password').placeholder = 'Mínimo 6 caracteres';
}

async function editUser(id) {
    try {
        const res = await axios.get(`${BASE}/api/users.php?action=get&id=${id}`);
        if (res.data.success) {
            const u = res.data.data.user;
            document.getElementById('user-id').value = u.id;
            document.querySelector('[name="name"]').value = u.name;
            document.querySelector('[name="email"]').value = u.email;
            document.querySelector('[name="role"]').value = u.role;
            
            document.getElementById('svc-modal-title').textContent = 'Editar Utilizador';
            document.getElementById('btn-label').textContent = 'Guardar Alterações';
            document.getElementById('user-password').required = false;
            document.getElementById('user-password').placeholder = 'Deixe em branco para manter a actual';
            
            openModal('user-modal');
        } else {
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao carregar dados.', 'error');
    }
}

function deleteUser(id, name) {
    confirmDelete(`Eliminar utilizador "${name}"? Esta acção não pode ser desfeita.`, async () => {
        try {
            const res = await axios.post(`${BASE}/api/users.php?action=delete&id=${id}`);
            if (res.data.success) {
                showToast(res.data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(res.data.message, 'error');
            }
        } catch (e) {
            showToast('Erro ao eliminar.', 'error');
        }
    });
}

async function toggleStatus(id) {
    try {
        const res = await axios.post(`${BASE}/api/users.php?action=toggle_status&id=${id}`);
        if (res.data.success) {
            showToast(res.data.message);
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao actualizar estado.', 'error');
    }
}
</script>
</body>
</html>
