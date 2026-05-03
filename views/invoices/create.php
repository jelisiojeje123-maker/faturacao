<?php
/**
 * View: Criar Fatura
 */
$pageTitle   = 'Criar Fatura';
$currentPage = 'create_inv';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1400px] w-full mx-auto">

            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900">Nova Fatura</h1>
                <p class="text-slate-500 text-sm mt-1">Preencha os dados abaixo para criar uma fatura</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Form Column -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Secção 1: Cliente -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm">1</span>
                            <h2 class="font-bold text-slate-900">Dados do Cliente</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cliente *</label>
                                <select id="client_id" name="client_id" required
                                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                                    <option value="">— Seleccionar cliente —</option>
                                    <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>" data-nuit="<?= htmlspecialchars($c['nuit'] ?? '') ?>" data-email="<?= htmlspecialchars($c['email'] ?? '') ?>">
                                        <?= htmlspecialchars($c['name']) ?><?= $c['nuit'] ? ' — NUIT: ' . $c['nuit'] : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nº Fatura</label>
                                <input type="text" id="invoice_number" name="invoice_number" readonly
                                       value="<?= htmlspecialchars($nextInvoiceNumber) ?>"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm bg-slate-100 text-slate-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Estado</label>
                                <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                                    <option value="rascunho">Rascunho</option>
                                    <option value="emitida" selected>Emitida</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Data de Emissão *</label>
                                <input type="date" name="issue_date" id="issue_date" value="<?= date('Y-m-d') ?>" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Data de Vencimento *</label>
                                <input type="date" name="due_date" id="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Secção 2: Itens -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm">2</span>
                                <h2 class="font-bold text-slate-900">Itens e Serviços</h2>
                            </div>
                            <button type="button" onclick="addItem()"
                                    class="flex items-center gap-1.5 text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                Adicionar Item
                            </button>
                        </div>

                        <!-- Header -->
                        <div class="grid grid-cols-12 gap-3 px-3 py-2 bg-slate-50 rounded-lg mb-3">
                            <div class="col-span-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Descrição</div>
                            <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Qtd</div>
                            <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Preço Unit.</div>
                            <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Total</div>
                            <div class="col-span-1"></div>
                        </div>

                        <!-- Items container -->
                        <div id="items-container" class="space-y-2"></div>

                        <!-- Service picker -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Adicionar serviço do catálogo
                            </label>
                            <select id="service-picker" onchange="addFromCatalog(this)"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                                <option value="">— Escolher serviço existente —</option>
                                <?php foreach ($services as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                        data-price="<?= $s['price'] ?>"
                                        data-name="<?= htmlspecialchars($s['name']) ?>">
                                    <?= htmlspecialchars($s['name']) ?> — <?= formatMoney((float)$s['price']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Secção 3: Notas -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm">3</span>
                            <h2 class="font-bold text-slate-900">Observações e Termos</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Observações</label>
                                <textarea name="notes" rows="4" placeholder="Obrigado pela preferência..."
                                          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Termos de Pagamento</label>
                                <textarea name="terms" rows="4" placeholder="Pagamento a 30 dias..."
                                          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Column -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden sticky top-24">
                        <!-- Dark header -->
                        <div class="bg-slate-900 px-6 py-5 text-white">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total a Pagar</span>
                                <span class="text-xs px-2 py-0.5 bg-white/10 rounded-full text-slate-300" id="status-badge">Emitida</span>
                            </div>
                            <div class="text-3xl font-black" id="total-display">MT 0,00</div>
                        </div>

                        <!-- Breakdown -->
                        <div class="p-6 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="font-semibold text-slate-700" id="subtotal-display">MT 0,00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">IVA (<?= $ivaRate ?>%)</span>
                                <span class="font-semibold text-slate-700" id="iva-display">MT 0,00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <label class="text-slate-500">Desconto</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-slate-400">MT</span>
                                    <input type="number" id="discount" name="discount" min="0" value="0" step="0.01"
                                           class="w-28 pl-7 pr-2 py-1 border border-slate-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-blue-500 outline-none"
                                           oninput="recalculate()">
                                </div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <label class="text-slate-500">Retenção</label>
                                <select id="retencao_rate" name="retencao_rate" onchange="recalculate()"
                                        class="w-28 px-2 py-1 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 outline-none bg-slate-50">
                                    <option value="0">Nenhuma</option>
                                    <option value="5">IRPC (5%)</option>
                                    <option value="20">IRPS (20%)</option>
                                </select>
                            </div>
                            <div class="flex justify-between text-xs text-rose-500 font-medium" id="retencao-row" style="display:none">
                                <span>Valor Retido</span>
                                <span id="retencao-display">MT 0,00</span>
                            </div>
                            <input type="hidden" name="iva_rate" value="<?= $ivaRate ?>">
                            <div class="pt-3 border-t border-slate-100 flex justify-between">
                                <span class="font-bold text-slate-900">Total Final</span>
                                <span class="text-xl font-black text-blue-600" id="total-final">MT 0,00</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 pb-6 space-y-3">
                            <button type="button" onclick="submitInvoice('emitida')"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
                                <span class="material-symbols-outlined text-[20px]">send</span>
                                Emitir Fatura
                            </button>
                            <button type="button" onclick="submitInvoice('rascunho')"
                                    class="w-full bg-white text-slate-600 border border-slate-200 font-semibold py-3 rounded-xl hover:bg-slate-50 transition-all flex items-center justify-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Guardar Rascunho
                            </button>
                        </div>

                        <!-- Methods accepted -->
                        <div class="px-6 pb-6">
                            <div class="bg-slate-50 rounded-xl p-4 border border-dashed border-slate-200">
                                <p class="text-xs font-bold text-slate-700 mb-2">Métodos de pagamento aceites</p>
                                <div class="space-y-1 text-xs text-slate-500">
                                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-green-600">check</span>Transferência Bancária</div>
                                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-green-600">check</span>M-Pesa / e-Mola</div>
                                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-green-600">check</span>Dinheiro</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>

<script>
const BASE  = '/faturacao';
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const IVA   = <?= $ivaRate ?>;
let itemCount = 0;

function addItem(description = '', quantity = 1, unitPrice = 0, serviceId = '') {
    itemCount++;
    const idx = itemCount;
    const container = document.getElementById('items-container');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 items-center bg-slate-50 rounded-xl px-3 py-2 group';
    row.id = `item-row-${idx}`;
    row.innerHTML = `
        <div class="col-span-5">
            <input type="hidden" name="items[${idx}][service_id]" value="${serviceId}" id="svc-${idx}">
            <input type="text" name="items[${idx}][description]" value="${description}"
                   placeholder="Descrição do serviço..." required
                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-500 outline-none">
        </div>
        <div class="col-span-2">
            <input type="number" name="items[${idx}][quantity]" value="${quantity}" min="0.01" step="0.01"
                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-center focus:ring-1 focus:ring-blue-500 outline-none"
                   oninput="recalcRow(${idx})">
        </div>
        <div class="col-span-2">
            <input type="number" name="items[${idx}][unit_price]" value="${unitPrice}" min="0" step="0.01"
                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-right focus:ring-1 focus:ring-blue-500 outline-none"
                   oninput="recalcRow(${idx})">
        </div>
        <div class="col-span-2 text-right">
            <span id="row-total-${idx}" class="text-sm font-bold text-slate-700">${formatMT(quantity * unitPrice)}</span>
        </div>
        <div class="col-span-1 text-right">
            <button type="button" onclick="removeItem(${idx})"
                    class="p-1 text-slate-300 hover:text-rose-500 transition-colors rounded">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        </div>
    `;
    container.appendChild(row);
    recalculate();
}

function addFromCatalog(sel) {
    if (!sel.value) return;
    const opt = sel.options[sel.selectedIndex];
    addItem(opt.dataset.name, 1, parseFloat(opt.dataset.price), sel.value);
    sel.value = '';
}

function removeItem(idx) {
    document.getElementById(`item-row-${idx}`)?.remove();
    recalculate();
}

function recalcRow(idx) {
    const qty   = parseFloat(document.querySelector(`[name="items[${idx}][quantity]"]`)?.value || 0);
    const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value || 0);
    const el    = document.getElementById(`row-total-${idx}`);
    if (el) el.textContent = formatMT(qty * price);
    recalculate();
}

function recalculate() {
    let subtotal = 0;
    document.querySelectorAll('[name^="items["]').forEach(input => {
        if (input.name.includes('[quantity]')) {
            const idx   = input.name.match(/\[(\d+)\]/)[1];
            const qty   = parseFloat(input.value || 0);
            const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value || 0);
            subtotal += qty * price;
        }
    });

    const iva      = subtotal * (IVA / 100);
    const discount = parseFloat(document.getElementById('discount').value || 0);
    const retRate  = parseFloat(document.getElementById('retencao_rate').value || 0);
    const retencao = subtotal * (retRate / 100);
    
    const total    = subtotal + iva - discount - retencao;

    document.getElementById('subtotal-display').textContent = formatMT(subtotal);
    document.getElementById('iva-display').textContent      = formatMT(iva);
    
    const retRow = document.getElementById('retencao-row');
    if (retencao > 0) {
        retRow.style.display = 'flex';
        document.getElementById('retencao-display').textContent = '- ' + formatMT(retencao);
    } else {
        retRow.style.display = 'none';
    }

    document.getElementById('total-display').textContent    = formatMT(total);
    document.getElementById('total-final').textContent      = formatMT(total);
}

async function submitInvoice(status) {
    const clientId = document.getElementById('client_id').value;
    if (!clientId) { showToast('Seleccione um cliente.', 'error'); return; }
    if (document.querySelectorAll('[name^="items["][name$="[description]"]').length === 0) {
        showToast('Adicione pelo menos um item.', 'error'); return;
    }

    // Collect form data
    const fd = new FormData();
    fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
    fd.append('client_id', clientId);
    fd.append('invoice_number', document.getElementById('invoice_number').value);
    fd.append('issue_date', document.getElementById('issue_date').value);
    fd.append('due_date', document.getElementById('due_date').value);
    fd.append('status', status);
    fd.append('iva_rate', IVA);
    fd.append('discount', document.getElementById('discount').value || 0);
    fd.append('retencao_rate', document.getElementById('retencao_rate').value || 0);

    // Notes & terms
    const notes = document.querySelector('[name="notes"]');
    const terms = document.querySelector('[name="terms"]');
    if (notes) fd.append('notes', notes.value);
    if (terms) fd.append('terms', terms.value);

    // Items
    let itemIdx = 0;
    document.querySelectorAll('[id^="item-row-"]').forEach(row => {
        const desc  = row.querySelector('[name*="[description]"]')?.value;
        const qty   = row.querySelector('[name*="[quantity]"]')?.value;
        const price = row.querySelector('[name*="[unit_price]"]')?.value;
        const svcId = row.querySelector('[name*="[service_id]"]')?.value;
        if (desc) {
            fd.append(`items[${itemIdx}][description]`, desc);
            fd.append(`items[${itemIdx}][quantity]`,    qty || 1);
            fd.append(`items[${itemIdx}][unit_price]`,  price || 0);
            if (svcId) fd.append(`items[${itemIdx}][service_id]`, svcId);
            itemIdx++;
        }
    });

    try {
        const res = await axios.post(`${BASE}/api/invoices.php?action=store`, fd);
        if (res.data.success) {
            showToast(`Fatura ${res.data.data.invoice_number} criada com sucesso!`);
            setTimeout(() => window.location.href = `${BASE}/faturas.php`, 1200);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch(e) {
        showToast('Erro ao criar fatura.', 'error');
    }
}

// Add first item row on load
addItem();
</script>
</body>
</html>
