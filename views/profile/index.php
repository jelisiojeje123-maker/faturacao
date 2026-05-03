<?php
/**
 * View: Perfil do Utilizador
 */
$pageTitle   = 'Meu Perfil';
$currentPage = 'profile';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[800px] w-full mx-auto space-y-6">
            
            <div class="mb-8">
                <h1 class="text-2xl font-black text-slate-900">O Meu Perfil</h1>
                <p class="text-slate-500 text-sm mt-1">Gerencie os seus dados pessoais e segurança da conta</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Avatar Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center justify-center text-center">
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-50 bg-slate-100 shadow-inner flex items-center justify-center">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="/faturacao/assets/img/avatars/<?= $user['avatar'] ?>" 
                                     class="w-full h-full object-cover" id="avatar-preview">
                            <?php else: ?>
                                <span class="text-4xl font-black text-slate-300"><?= getInitials($user['name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <button onclick="document.getElementById('avatar-input').click()"
                                class="absolute bottom-0 right-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center border-4 border-white shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                        </button>
                        <input type="file" id="avatar-input" class="hidden" accept="image/*" onchange="uploadAvatar(this)">
                    </div>
                    <h3 class="mt-4 font-black text-slate-900"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold"><?= $user['role'] ?></p>
                    <div id="avatar-loader" class="hidden mt-3">
                        <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                
                <!-- Informações Básicas -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">person</span>
                            Dados Pessoais
                        </h2>
                    </div>
                    <form id="profile-form" class="p-6 space-y-4">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nome Completo</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email de Acesso</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-blue-600/20 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Actualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Alterar Senha -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">lock</span>
                            Segurança (Alterar Senha)
                        </h2>
                    </div>
                    <form id="password-form" class="p-6 space-y-4">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Senha Actual</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nova Senha</label>
                                <input type="password" name="new_password" required minlength="6"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Confirmar Nova Senha</label>
                                <input type="password" name="confirm_password" required minlength="6"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-slate-800/20 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">key</span>
                                Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>

                </div>

            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/faturacao';

document.getElementById('profile-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
        const fd = new FormData(this);
        const res = await axios.post(`${BASE}/api/profile.php?action=update`, fd);
        if (res.data.success) {
            showToast(res.data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao actualizar perfil.', 'error');
    } finally {
        btn.disabled = false;
    }
});

document.getElementById('password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    try {
        const fd = new FormData(this);
        const res = await axios.post(`${BASE}/api/profile.php?action=change_password`, fd);
        if (res.data.success) {
            showToast(res.data.message);
            this.reset();
        } else {
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao alterar senha.', 'error');
    } finally {
        btn.disabled = false;
    }
});

async function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    
    const loader = document.getElementById('avatar-loader');
    loader.classList.remove('hidden');
    
    try {
        const fd = new FormData();
        fd.append('<?= CSRF_TOKEN_NAME ?>', '<?= generateCsrfToken() ?>');
        fd.append('avatar', input.files[0]);
        
        const res = await axios.post(`${BASE}/api/profile.php?action=update_avatar`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (res.data.success) {
            showToast('Avatar actualizado!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        showToast('Erro ao carregar imagem.', 'error');
    } finally {
        loader.classList.add('hidden');
    }
}
</script>
</body>
</html>
