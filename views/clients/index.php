<?php
/**
 * View: Lista de Clientes
 */
$pageTitle   = 'Clientes';
$currentPage = 'clients';
$extraHead = '<meta name="csrf-token" content="' . generateCsrfToken() . '">';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Clientes</h1>
                    <p class="text-slate-500 text-sm mt-1"><?= number_format($total) ?> clientes registados</p>
                </div>
                <button onclick="openClientModal()"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Novo Cliente
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Pesquisar por nome, email, NUIT ou telefone..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white transition-all">
                    </div>
                    <select name="status" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="">Todos os estados</option>
                        <option value="ativo"    <?= ($status ?? '') === 'ativo'    ? 'selected' : '' ?>>Ativo</option>
                        <option value="inativo"  <?= ($status ?? '') === 'inativo'  ? 'selected' : '' ?>>Inativo</option>
                        <option value="pendente" <?= ($status ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Filtrar
                    </button>
                    <?php if (!empty($search) || !empty($status)): ?>
                    <a href="/faturacao/clientes.php" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">close</span> Limpar
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Contacto</th>
                            <th class="px-6 py-3 text-left">NUIT</th>
                            <th class="px-6 py-3 text-left">Localidade</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $client): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        <?= getInitials($client['name']) ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900"><?= htmlspecialchars($client['name']) ?></p>
                                        <p class="text-xs text-slate-400">ID #<?= $client['id'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-700"><?= htmlspecialchars($client['email'] ?: '—') ?></p>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($client['phone'] ?: '—') ?></p>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600 text-xs"><?= htmlspecialchars($client['nuit'] ?: '—') ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($client['city'] ?: '—') ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $sc = match($client['status']) {
                                    'ativo'    => 'bg-emerald-100 text-emerald-700',
                                    'inativo'  => 'bg-rose-100 text-rose-700',
                                    'pendente' => 'bg-amber-100 text-amber-700',
                                    default    => 'bg-slate-100 text-slate-600',
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= $sc ?>">
                                    <?= ucfirst($client['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="editClient(<?= htmlspecialchars(json_encode($client)) ?>)"
                                            class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <a href="/faturacao/faturas.php?client_id=<?= $client['id'] ?>"
                                       class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all" title="Ver Faturas">
                                        <span class="material-symbols-outlined text-[18px]">description</span>
                                    </a>
                                    <button onclick="deleteClient(<?= $client['id'] ?>, '<?= htmlspecialchars(addslashes($client['name'])) ?>')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Eliminar">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">group</span>
                            Nenhum cliente encontrado.
                            <button onclick="openClientModal()" class="block mx-auto mt-3 text-blue-600 font-semibold text-sm hover:underline">Adicionar o primeiro cliente →</button>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($last_page > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-sm">
                    <p class="text-slate-500">
                        Mostrando <?= count($items) ?> de <?= $total ?> clientes
                    </p>
                    <div class="flex gap-1">
                        <?php for ($p = 1; $p <= $last_page; $p++): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status ?? '') ?>"
                           class="px-3 py-1.5 rounded-lg font-semibold <?= $p === $page ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-200' ?> transition-all">
                            <?= $p ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Modal: Criar/Editar Cliente -->
<div id="client-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 modal-backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modal-title" class="text-lg font-bold text-slate-900">Novo Cliente</h3>
            <button onclick="closeClientModal()" class="text-slate-400 hover:text-slate-700 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="client-form" class="p-6 space-y-4">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
            <input type="hidden" id="client-id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Nome / Empresa *</label>
                    <input type="text" name="name" id="f-name" required placeholder="Nome completo ou empresa"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">NUIT</label>
                    <input type="text" name="nuit" id="f-nuit" placeholder="400 000 000"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Telefone</label>
                    <input type="text" name="phone" id="f-phone" placeholder="+258 84 000 0000"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" id="f-email" placeholder="cliente@email.com"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Cidade</label>
                    <input type="text" name="city" id="f-city" placeholder="Maputo"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Estado</label>
                    <select name="status" id="f-status"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Endereço</label>
                    <input type="text" name="address" id="f-address" placeholder="Av. ..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Notas</label>
                    <textarea name="notes" id="f-notes" rows="2" placeholder="Observações..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-none"></textarea>
                </div>
            </div>

            <div id="form-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeClientModal()"
                        class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" id="btn-save"
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-all active:scale-95">
                    Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>

<script>
const BASE = '/faturacao';
const metaCsrfToken = document.querySelector('meta[name="csrf-token"]');
const CSRF = metaCsrfToken ? metaCsrfToken.content : '';

function openClientModal(client) {
    var modal = document.getElementById('client-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('modal-title').textContent = client ? 'Editar Cliente' : 'Novo Cliente';
    document.getElementById('client-id').value = client && client.id ? client.id : '';
    document.getElementById('f-name').value   = client && client.name ? client.name : '';
    document.getElementById('f-nuit').value   = client && client.nuit ? client.nuit : '';
    document.getElementById('f-email').value  = client && client.email ? client.email : '';
    document.getElementById('f-phone').value  = client && client.phone ? client.phone : '';
    document.getElementById('f-city').value   = client && client.city ? client.city : '';
    document.getElementById('f-address').value = client && client.address ? client.address : '';
    document.getElementById('f-status').value = client && client.status ? client.status : 'ativo';
    document.getElementById('f-notes').value  = client && client.notes ? client.notes : '';
    document.getElementById('form-error').classList.add('hidden');
}

function closeClientModal() {
    var modal = document.getElementById('client-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

function editClient(client) { openClientModal(client); }

function deleteClient(id, name) {
    confirmDelete(`Eliminar o cliente "${name}"? Esta acção é irreversível.`, async () => {
        try {
            const fd = new FormData();
            fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
            fd.append('_method', 'DELETE');
            const res = await axios.post(`${BASE}/api/clients.php?id=${id}&action=delete`, fd);
            if (res.data.success) { showToast('Cliente eliminado!'); setTimeout(()=>location.reload(), 800); }
            else showToast(res.data.message, 'error');
        } catch(e) { showToast('Erro ao eliminar.', 'error'); }
    });
}

document.getElementById('client-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id  = document.getElementById('client-id').value;
    const btn = document.getElementById('btn-save');
    const errDiv = document.getElementById('form-error');
    btn.disabled = true; btn.textContent = 'A guardar...';
    errDiv.classList.add('hidden');

    try {
        const fd = new FormData(this);
        const url = id ? `${BASE}/api/clients.php?id=${id}&action=update` : `${BASE}/api/clients.php?action=store`;
        const res = await axios.post(url, fd);

        if (res.data.success) {
            showToast(res.data.message);
            closeClientModal();
            setTimeout(() => location.reload(), 800);
        } else {
            errDiv.textContent = res.data.message;
            errDiv.classList.remove('hidden');
        }
    } catch(e) {
        errDiv.textContent = 'Erro de comunicação com o servidor.';
        errDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = 'Guardar Cliente';
    }
});

// Close modal on backdrop click
document.getElementById('client-modal').addEventListener('click', function(e) {
    if (e.target === this) closeClientModal();
});
</script>
</body>
</html>
