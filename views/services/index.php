<?php
/**
 * View: Serviços
 */
$pageTitle   = 'Serviços';
$currentPage = 'services';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Serviços</h1>
                    <p class="text-slate-500 text-sm mt-1"><?= number_format($total) ?> serviços no catálogo</p>
                </div>
                <button onclick="openServiceModal()"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Novo Serviço
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Pesquisar serviço..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                    </div>
                    <select name="type" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos os tipos</option>
                        <option value="pontual"    <?= ($type ?? '') === 'pontual'    ? 'selected' : '' ?>>Pontual</option>
                        <option value="recorrente" <?= ($type ?? '') === 'recorrente' ? 'selected' : '' ?>>Recorrente</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Grid of service cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($items as $svc): ?>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition-shadow group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-[22px]">
                                <?= $svc['type'] === 'recorrente' ? 'autorenew' : 'handyman' ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="editService(<?= htmlspecialchars(json_encode($svc)) ?>)"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <button onclick="deleteService(<?= $svc['id'] ?>, '<?= htmlspecialchars(addslashes($svc['name'])) ?>')"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1"><?= htmlspecialchars($svc['name']) ?></h3>
                    <?php if ($svc['description']): ?>
                    <p class="text-slate-500 text-xs mb-3 line-clamp-2"><?= htmlspecialchars($svc['description']) ?></p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-100">
                        <div>
                            <p class="text-lg font-black text-blue-600"><?= formatMoney((float)$svc['price']) ?></p>
                            <p class="text-xs text-slate-400">por <?= htmlspecialchars($svc['unit']) ?></p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                <?= $svc['type'] === 'recorrente' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' ?>">
                                <?= ucfirst($svc['type']) ?>
                            </span>
                            <?php if ($svc['iva_exempt']): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Isento IVA</span>
                            <?php endif; ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $svc['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                <?= $svc['is_active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <div class="col-span-full py-16 text-center text-slate-400">
                    <span class="material-symbols-outlined text-5xl block mb-3 text-slate-300">build_circle</span>
                    <p>Nenhum serviço encontrado.</p>
                    <button onclick="openServiceModal()" class="mt-3 text-blue-600 font-semibold text-sm hover:underline">Criar o primeiro →</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($last_page > 1): ?>
            <div class="flex justify-center gap-1">
                <?php for ($p = 1; $p <= $last_page; $p++): ?>
                <a href="?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&type=<?= urlencode($type ?? '') ?>"
                   class="px-3 py-1.5 rounded-lg font-semibold text-sm <?= $p === $page ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?> transition-all">
                    <?= $p ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal: Criar/Editar Serviço -->
<div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 modal-backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 id="svc-modal-title" class="text-lg font-bold text-slate-900">Novo Serviço</h3>
            <button onclick="closeServiceModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="service-form" class="p-6 space-y-4">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
            <input type="hidden" id="svc-id" value="">

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Nome do Serviço *</label>
                <input type="text" name="name" id="svc-name" required placeholder="Ex: Consultoria de TI"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Descrição</label>
                <textarea name="description" id="svc-desc" rows="2" placeholder="Descrição opcional do serviço..."
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Preço (MT) *</label>
                    <input type="number" name="price" id="svc-price" min="0" step="0.01" required placeholder="0,00"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Unidade</label>
                    <select name="unit" id="svc-unit"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="un">Unidade</option>
                        <option value="hora">Hora</option>
                        <option value="dia">Dia</option>
                        <option value="semana">Semana</option>
                        <option value="mês">Mês</option>
                        <option value="ano">Ano</option>
                        <option value="projecto">Projecto</option>
                        <option value="sessão">Sessão</option>
                        <option value="km">Km</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Tipo</label>
                    <select name="type" id="svc-type"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="pontual">Pontual</option>
                        <option value="recorrente">Recorrente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Estado</label>
                    <select name="is_active" id="svc-active"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="svc-iva-exempt" name="iva_exempt" value="1"
                       class="w-4 h-4 text-blue-600 rounded border-slate-300">
                <label for="svc-iva-exempt" class="text-sm text-slate-700">Isento de IVA</label>
            </div>

            <div id="svc-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeServiceModal()"
                        class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all active:scale-95">
                    Guardar Serviço
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/Sistema%20de%20Faturacao';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openServiceModal(s = null) {
    document.getElementById('service-modal').classList.replace('hidden','flex');
    document.getElementById('svc-modal-title').textContent = s ? 'Editar Serviço' : 'Novo Serviço';
    document.getElementById('svc-id').value       = s?.id ?? '';
    document.getElementById('svc-name').value     = s?.name ?? '';
    document.getElementById('svc-desc').value     = s?.description ?? '';
    document.getElementById('svc-price').value    = s?.price ?? '';
    document.getElementById('svc-unit').value     = s?.unit ?? 'un';
    document.getElementById('svc-type').value     = s?.type ?? 'pontual';
    document.getElementById('svc-active').value   = s ? (s.is_active ? '1' : '0') : '1';
    document.getElementById('svc-iva-exempt').checked = s?.iva_exempt == 1;
    document.getElementById('svc-error').classList.add('hidden');
}
function closeServiceModal() { document.getElementById('service-modal').classList.replace('flex','hidden'); }
function editService(s) { openServiceModal(s); }

function deleteService(id, name) {
    confirmDelete(`Eliminar "${name}"?`, async () => {
        try {
            const fd = new FormData();
            fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
            const res = await axios.post(`${BASE}/api/services.php?id=${id}&action=delete`, fd);
            if (res.data.success) { showToast('Serviço eliminado!'); setTimeout(()=>location.reload(), 700); }
            else showToast(res.data.message, 'error');
        } catch(e) { showToast('Erro ao eliminar.','error'); }
    });
}

document.getElementById('service-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id  = document.getElementById('svc-id').value;
    const err = document.getElementById('svc-error');
    err.classList.add('hidden');
    try {
        const fd = new FormData(this);
        if (!document.getElementById('svc-iva-exempt').checked) fd.set('iva_exempt','0');
        const url = id ? `${BASE}/api/services.php?id=${id}&action=update` : `${BASE}/api/services.php?action=store`;
        const res = await axios.post(url, fd);
        if (res.data.success) { showToast(res.data.message); closeServiceModal(); setTimeout(()=>location.reload(),700); }
        else { err.textContent = res.data.message; err.classList.remove('hidden'); }
    } catch(e) { err.textContent='Erro de comunicação.'; err.classList.remove('hidden'); }
});
document.getElementById('service-modal').addEventListener('click', e => { if(e.target===e.currentTarget) closeServiceModal(); });
</script>
</body>
</html>
