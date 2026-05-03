<?php
/**
 * View: Criar Orçamento
 */
$pageTitle   = 'Criar Orçamento';
$currentPage = 'create_quote';

// Gerar número temporário
$prefix = $companySettings['invoice_prefix'] ?? 'ORC';
$nextId = (int)Database::getInstance()->query("SELECT MAX(id) FROM quotes")->fetchColumn() + 1;
$nextQuoteNumber = sprintf("%s-ORC-%s-%04d", $prefix, date('Y'), $nextId);
$ivaRate = (float)($companySettings['iva_rate'] ?? 16);

$extraHead = '<meta name="csrf-token" content="'.generateCsrfToken().'">';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1400px] w-full mx-auto">

            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900">Novo Orçamento</h1>
                <p class="text-slate-500 text-sm mt-1">Preencha os dados abaixo para criar uma proposta comercial</p>
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
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['name']) ?><?= $c['nuit'] ? ' — NUIT: ' . $c['nuit'] : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nº Orçamento</label>
                                <input type="text" id="quote_number" name="quote_number" readonly
                                       value="<?= htmlspecialchars($nextQuoteNumber) ?>"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm bg-slate-100 text-slate-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Estado</label>
                                <select id="status" name="status" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                                    <option value="rascunho">Rascunho</option>
                                    <option value="enviado" selected>Enviado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Data de Emissão *</label>
                                <input type="date" name="issue_date" id="issue_date" value="<?= date('Y-m-d') ?>" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Validade (Data)</label>
                                <input type="date" name="expiry_date" id="expiry_date" value="<?= date('Y-m-d', strtotime('+15 days')) ?>"
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
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Adicionar do Catálogo</label>
                            <select onchange="addFromCatalog(this)" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                                <option value="">— Escolha um serviço pré-definido —</option>
                                <?php foreach ($services as $s): ?>
                                <option value="<?= $s['id'] ?>" data-name="<?= htmlspecialchars($s['name']) ?>" data-price="<?= $s['price'] ?>">
                                    <?= htmlspecialchars($s['name']) ?> (<?= formatMoney((float)$s['price']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Secção 3: Notas e Condições -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm">3</span>
                            <h2 class="font-bold text-slate-900">Observações</h2>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Notas visíveis no orçamento</label>
                                <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-y" placeholder="Agradecemos a preferência..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Termos e Condições</label>
                                <textarea name="terms" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white resize-y"><?= htmlspecialchars($companySettings['payment_terms'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary & Actions -->
                <div class="space-y-5 relative">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm sticky top-6">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-lg mb-4">Resumo</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center text-slate-600">
                                    <span>Subtotal</span>
                                    <span id="subtotal-display" class="font-semibold">0,00 MT</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-600">
                                    <span>IVA (<?= $ivaRate ?>%)</span>
                                    <span id="iva-display" class="font-semibold">0,00 MT</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                    <span class="text-slate-600">Desconto (MT)</span>
                                    <input type="number" id="discount" name="discount" value="0" min="0" step="0.01" oninput="recalculate()"
                                           class="w-24 px-2 py-1 border border-slate-200 rounded text-right text-sm focus:ring-1 focus:ring-blue-500 outline-none bg-slate-50">
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <div class="flex justify-between items-end">
                                    <span class="font-black text-slate-900">TOTAL</span>
                                    <span id="total-final" class="text-2xl font-black text-blue-600">0,00 MT</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-3">
                            <button type="button" onclick="submitQuote()"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 active:scale-95 shadow-lg shadow-blue-600/30">
                                <span class="material-symbols-outlined text-[20px]">send</span>
                                Criar Orçamento
                            </button>
                            <button type="button" onclick="submitQuote('rascunho')"
                                    class="w-full bg-white text-slate-600 border border-slate-200 font-semibold py-3 rounded-xl hover:bg-slate-50 transition-all flex items-center justify-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Guardar Rascunho
                            </button>
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
            <input type="hidden" name="items[${idx}][service_id]" value="${serviceId}">
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
    try {
        let subtotal = 0;
        // Seleccionar apenas os inputs de quantidade para iterar pelos itens
        const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]');
        
        quantityInputs.forEach(qtyInput => {
            const name = qtyInput.name;
            const match = name.match(/items\[(\d+)\]/);
            if (match) {
                const idx = match[1];
                const qty = parseFloat(qtyInput.value || 0);
                const priceInput = document.querySelector(`input[name="items[${idx}][unit_price]"]`);
                const price = parseFloat(priceInput ? priceInput.value : 0);
                subtotal += qty * price;
            }
        });

        const ivaAmount = subtotal * (IVA / 100);
        const discountInput = document.getElementById('discount');
        const discount = parseFloat(discountInput ? discountInput.value : 0);
        const total = subtotal + ivaAmount - discount;

        // Actualizar displays
        const subtotalEl = document.getElementById('subtotal-display');
        const ivaEl = document.getElementById('iva-display');
        const totalEl = document.getElementById('total-final');

        if (subtotalEl) subtotalEl.textContent = formatMT(subtotal);
        if (ivaEl)      ivaEl.textContent      = formatMT(ivaAmount);
        if (totalEl)    totalEl.textContent    = formatMT(total);
        
    } catch (err) {
        console.error('Erro no cálculo:', err);
    }
}

async function submitQuote(overrideStatus = null) {
    const clientId = document.getElementById('client_id').value;
    if (!clientId) { showToast('Seleccione um cliente.', 'error'); return; }
    if (document.querySelectorAll('[name^="items["][name$="[description]"]').length === 0) {
        showToast('Adicione pelo menos um item.', 'error'); return;
    }

    // Collect form data
    const fd = new FormData();
    fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
    fd.append('client_id', clientId);
    fd.append('issue_date', document.getElementById('issue_date').value);
    fd.append('expiry_date', document.getElementById('expiry_date').value);
    fd.append('status', overrideStatus || document.getElementById('status').value);
    
    // Totals
    let subtotal = 0;
    document.querySelectorAll('[name^="items["]').forEach(input => {
        if (input.name.includes('[quantity]')) {
            const idx   = input.name.match(/\[(\d+)\]/)[1];
            const qty   = parseFloat(input.value || 0);
            const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value || 0);
            subtotal += qty * price;
        }
    });
    const iva = subtotal * (IVA / 100);
    const discount = parseFloat(document.getElementById('discount').value || 0);
    
    fd.append('subtotal', subtotal);
    fd.append('iva_amount', iva);
    fd.append('discount', discount);
    fd.append('total', subtotal + iva - discount);

    // Notes & terms
    const notes = document.querySelector('[name="notes"]');
    const terms = document.querySelector('[name="terms"]');
    if (notes) fd.append('notes', notes.value);
    if (terms) fd.append('terms', terms.value);

    // Items
    let itemIdx = 0;
    const itemRows = document.querySelectorAll('[id^="item-row-"]');
    if (itemRows.length === 0) {
        showToast('Adicione pelo menos um item.', 'error');
        return;
    }

    itemRows.forEach(row => {
        const descInput  = row.querySelector('[name*="[description]"]');
        const qtyInput   = row.querySelector('[name*="[quantity]"]');
        const priceInput = row.querySelector('[name*="[unit_price]"]');
        const svcInput   = row.querySelector('[name*="[service_id]"]');

        const desc  = descInput ? descInput.value.trim() : '';
        const qty   = qtyInput ? qtyInput.value : 1;
        const price = priceInput ? priceInput.value : 0;
        const svcId = svcInput ? svcInput.value : '';

        if (desc) {
            fd.append(`item_description[${itemIdx}]`, desc);
            fd.append(`item_quantity[${itemIdx}]`,    qty);
            fd.append(`item_price[${itemIdx}]`,       price);
            fd.append(`item_total[${itemIdx}]`,       (parseFloat(qty || 1) * parseFloat(price || 0)));
            if (svcId) fd.append(`item_service_id[${itemIdx}]`, svcId);
            itemIdx++;
        }
    });

    if (itemIdx === 0) {
        showToast('Preencha a descrição de pelo menos um item.', 'error');
        return;
    }

    try {
        const res = await axios.post(`${BASE}/api/quotes?action=store`, fd);
        if (res.data.success) {
            showToast(`Orçamento criado com sucesso!`);
            setTimeout(() => window.location.href = `${BASE}/orcamentos`, 1200);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch(e) {
        showToast('Erro ao criar orçamento.', 'error');
    }
}

// Add first item row on load
addItem();
</script>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
</body>
</html>
