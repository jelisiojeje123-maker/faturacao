<?php
/**
 * View: Configurações da Empresa
 */
$pageTitle   = 'Configurações';
$currentPage = 'settings';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1000px] w-full mx-auto">
            
            <div class="mb-8">
                <h1 class="text-2xl font-black text-slate-900">Configurações do Sistema</h1>
                <p class="text-slate-500 text-sm mt-1">Gerencie os dados da sua empresa e preferências de faturação</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Logo Card -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center justify-center text-center">
                    <div class="relative group">
                        <div class="w-40 h-28 rounded-xl overflow-hidden border-2 border-slate-50 bg-slate-100 shadow-inner flex items-center justify-center p-2">
                            <?php if (!empty($settings['logo'])): ?>
                                <img src="/faturacao/assets/img/logo/<?= $settings['logo'] ?>" 
                                     class="max-w-full max-h-full object-contain" id="logo-preview">
                            <?php else: ?>
                                <div class="text-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-300">image</span>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Logo</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button onclick="document.getElementById('logo-input').click()"
                                class="absolute -bottom-2 -right-2 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center border-4 border-white shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                        </button>
                        <input type="file" id="logo-input" class="hidden" accept="image/*" onchange="uploadLogo(this)">
                    </div>
                    <div class="mt-4">
                        <h3 class="font-bold text-slate-900 text-sm">Logotipo da Empresa</h3>
                        <p class="text-xs text-slate-500 mt-1">Recomendado: PNG Transparente</p>
                    </div>
                    <div id="logo-loader" class="hidden mt-3">
                        <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <form id="settings-form" class="space-y-6">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">

                <!-- Secção: Dados da Empresa -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">business</span>
                            Dados da Empresa
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nome da Empresa *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($settings['name']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">NUIT *</label>
                            <input type="text" name="nuit" value="<?= htmlspecialchars($settings['nuit']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cidade *</label>
                            <input type="text" name="city" value="<?= htmlspecialchars($settings['city']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email de Contacto *</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($settings['email']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Telefone *</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($settings['phone']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Endereço Completo *</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($settings['address']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Secção: Preferências de Faturação -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">receipt_long</span>
                            Preferências de Faturação
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Moeda Padrão</label>
                            <input type="text" name="currency" value="<?= htmlspecialchars($settings['currency']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Taxa de IVA (%)</label>
                            <input type="number" name="iva_rate" value="<?= (float)$settings['iva_rate'] ?>" step="0.01" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Prefixo de Faturas</label>
                            <input type="text" name="invoice_prefix" value="<?= htmlspecialchars($settings['invoice_prefix']) ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Próximo Número</label>
                            <input type="number" name="next_invoice_number" value="<?= (int)$settings['next_invoice_number'] ?>" required
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Termos de Pagamento Padrão</label>
                            <textarea name="payment_terms" rows="3"
                                      class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-none"><?= htmlspecialchars($settings['payment_terms']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Secção: Dados Bancários -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">account_balance</span>
                            Dados Bancários (Para Faturas)
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nome do Banco</label>
                            <input type="text" name="bank_name" value="<?= htmlspecialchars($settings['bank_name']) ?>"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Número da Conta / NIB</label>
                            <input type="text" name="bank_account" value="<?= htmlspecialchars($settings['bank_account']) ?>"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                    </div>
                </div>

                <div id="settings-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>
                <div id="settings-success" class="hidden px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-semibold"></div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="submit"
                            class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Guardar Alterações
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
document.getElementById('settings-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn    = this.querySelector('button[type="submit"]');
    const errDiv = document.getElementById('settings-error');
    const sucDiv = document.getElementById('settings-success');
    
    errDiv.classList.add('hidden');
    sucDiv.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin material-symbols-outlined">sync</span> Guardando...';

    try {
        const fd  = new FormData(this);
        const res = await axios.post('/faturacao/api/settings.php?action=store', fd);
        
        if (res.data.success) {
            sucDiv.textContent = res.data.message;
            sucDiv.classList.remove('hidden');
            showToast(res.data.message);
        } else {
            errDiv.textContent = res.data.message;
            errDiv.classList.remove('hidden');
            showToast(res.data.message, 'error');
        }
    } catch (e) {
        errDiv.textContent = 'Erro ao comunicar com o servidor.';
        errDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">save</span> Guardar Alterações';
    }
});

async function uploadLogo(input) {
    if (!input.files || !input.files[0]) return;
    
    const loader = document.getElementById('logo-loader');
    loader.classList.remove('hidden');
    
    try {
        const fd = new FormData();
        fd.append('<?= CSRF_TOKEN_NAME ?>', '<?= generateCsrfToken() ?>');
        fd.append('logo', input.files[0]);
        
        const res = await axios.post('/faturacao/api/settings.php?action=update_logo', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (res.data.success) {
            showToast('Logotipo actualizado!');
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
