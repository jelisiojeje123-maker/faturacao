<?php
/**
 * View: Lista de Faturas
 */
$pageTitle   = 'Faturas';
$currentPage = 'invoices';
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
                    <h1 class="text-2xl font-black text-slate-900">Faturas</h1>
                    <p class="text-slate-500 text-sm mt-1"><?= number_format($total) ?> faturas no sistema</p>
                </div>
                <a href="/faturacao/criar-fatura.php"
                   class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Nova Fatura
                </a>
            </div>

            <!-- Status filter pills -->
            <div class="flex items-center gap-2 flex-wrap">
                <?php
                $statuses = ['' => 'Todas', 'rascunho' => 'Rascunho', 'emitida' => 'Emitidas', 'paga' => 'Pagas', 'vencida' => 'Vencidas', 'cancelada' => 'Canceladas'];
                foreach ($statuses as $val => $label):
                    $active = ($status ?? '') === $val;
                ?>
                <a href="?status=<?= urlencode($val) ?>&search=<?= urlencode($search ?? '') ?>"
                   class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all
                          <?= $active ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <form method="GET" class="flex gap-3">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status ?? '') ?>">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Pesquisar por número de fatura ou cliente..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Pesquisar
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-3 text-left">Fatura</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Emissão</th>
                            <th class="px-6 py-3 text-left">Vencimento</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-right">Em Dívida</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $inv): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="/faturacao/faturas.php?id=<?= $inv['id'] ?>"
                                   class="font-mono text-blue-600 hover:underline text-xs font-semibold">
                                    <?= htmlspecialchars($inv['invoice_number']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                                        <?= getInitials($inv['client_name']) ?>
                                    </div>
                                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($inv['client_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= formatDate($inv['issue_date']) ?></td>
                            <td class="px-6 py-4 text-slate-500 <?= (strtotime($inv['due_date']) < time() && $inv['status'] === 'emitida') ? 'text-rose-500 font-semibold' : '' ?>">
                                <?= formatDate($inv['due_date']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900"><?= formatMoney((float)$inv['total']) ?></td>
                            <td class="px-6 py-4 text-right <?= (float)$inv['amount_due'] > 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600' ?>">
                                <?= (float)$inv['amount_due'] > 0 ? formatMoney((float)$inv['amount_due']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= invoiceStatusClass($inv['status']) ?>">
                                    <?= invoiceStatusLabel($inv['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/faturacao/faturas.php?id=<?= $inv['id'] ?>"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver detalhes">
                                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                    </a>
                                    <a href="/faturacao/faturas.php?action=print&id=<?= $inv['id'] ?>" target="_blank"
                                       class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all" title="Imprimir/PDF">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                    </a>
                                    <?php if ($inv['status'] !== 'paga' && $inv['status'] !== 'cancelada'): ?>
                                    <button onclick="openPaymentModal(<?= $inv['id'] ?>, '<?= htmlspecialchars(addslashes($inv['invoice_number'])) ?>', <?= $inv['amount_due'] ?>)"
                                            class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Registar Pagamento">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr><td colspan="8" class="px-6 py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">description</span>
                            Nenhuma fatura encontrada.
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($last_page > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-sm">
                    <p class="text-slate-500">Mostrando <?= count($items) ?> de <?= $total ?></p>
                    <div class="flex gap-1">
                        <?php for ($p = 1; $p <= min($last_page, 10); $p++): ?>
                        <a href="?page=<?= $p ?>&status=<?= urlencode($status ?? '') ?>&search=<?= urlencode($search ?? '') ?>"
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

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 modal-backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Registar Pagamento</h3>
                <p class="text-xs text-slate-400 mt-0.5" id="pay-invoice-label"></p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="payment-form" class="p-6 space-y-4">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="invoice_id" id="pay-invoice-id">

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Valor *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm font-semibold">MT</span>
                    <input type="number" name="amount" id="pay-amount" min="0.01" step="0.01" required
                           class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                </div>
                <p class="text-xs text-slate-400 mt-1">Valor máximo: <span id="pay-max" class="font-semibold text-slate-600"></span></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Data *</label>
                <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Método *</label>
                <select name="method" required class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                    <option value="transferencia">Transferência Bancária</option>
                    <option value="mobile_money">Mobile Money (M-Pesa / e-Mola)</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cheque">Cheque</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Referência</label>
                <input type="text" name="reference" placeholder="Ref. da transação (opcional)"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
            </div>

            <div id="pay-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closePaymentModal()"
                        class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all">
                    Registar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/faturacao';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openPaymentModal(invoiceId, invoiceNumber, amountDue) {
    document.getElementById('payment-modal').classList.replace('hidden','flex');
    document.getElementById('pay-invoice-id').value    = invoiceId;
    document.getElementById('pay-invoice-label').textContent = 'Fatura ' + invoiceNumber;
    document.getElementById('pay-amount').value        = amountDue;
    document.getElementById('pay-max').textContent     = formatMT(amountDue);
    document.getElementById('pay-error').classList.add('hidden');
}
function closePaymentModal() {
    document.getElementById('payment-modal').classList.replace('flex','hidden');
}

document.getElementById('payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('pay-error');
    errDiv.classList.add('hidden');
    try {
        const fd = new FormData(this);
        const res = await axios.post(`${BASE}/api/payments.php?action=store`, fd);
        if (res.data.success) {
            showToast(`Pagamento registado! Recibo: ${res.data.data.receipt_number}`);
            closePaymentModal();
            setTimeout(() => location.reload(), 900);
        } else {
            errDiv.textContent = res.data.message;
            errDiv.classList.remove('hidden');
        }
    } catch(e) {
        errDiv.textContent = 'Erro ao comunicar com o servidor.';
        errDiv.classList.remove('hidden');
    }
});
document.getElementById('payment-modal').addEventListener('click', e => { if(e.target===e.currentTarget) closePaymentModal(); });
</script>
</body>
</html>
