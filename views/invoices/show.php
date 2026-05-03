<?php
/**
 * View: Detalhe da Fatura (show)
 */
$pageTitle   = 'Fatura ' . $invoice['invoice_number'];
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

        <div class="p-6 lg:p-8 max-w-[1400px] w-full mx-auto space-y-6">

            <!-- Breadcrumb + Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <a href="/faturacao/faturas" class="hover:text-blue-600 transition-colors">Faturas</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="font-semibold text-slate-900 font-mono"><?= htmlspecialchars($invoice['invoice_number']) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/faturacao/faturas?action=print&id=<?= $invoice['id'] ?>" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 border border-slate-200 bg-white text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Imprimir / PDF
                    </a>

                    <?php if ($invoice['status'] !== 'paga' && $invoice['status'] !== 'cancelada'): ?>
                    <button onclick="openPaymentModal()"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        Registar Pagamento
                    </button>
                    <?php if ($invoice['status'] !== 'cancelada' && $invoice['status'] !== 'paga'): ?>
                    <button onclick="changeStatus('cancelada')"
                            class="flex items-center gap-2 px-4 py-2 border border-rose-200 bg-white text-rose-600 rounded-xl text-sm font-semibold hover:bg-rose-50 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Anular Fatura
                    </button>
                    <?php endif; ?>
                <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Invoice Main Content -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Invoice Header Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="bg-slate-900 px-8 py-6 text-white">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Fatura</p>
                                    <h2 class="text-2xl font-black font-mono"><?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                                    <p class="text-slate-400 text-sm mt-2">
                                        Emitida em <?= formatDate($invoice['issue_date']) ?>
                                        &bull; Vence em <?= formatDate($invoice['due_date']) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1.5 rounded-full text-sm font-black uppercase
                                        <?= match($invoice['status']) {
                                            'paga'     => 'bg-emerald-500 text-white',
                                            'emitida'  => 'bg-blue-500 text-white',
                                            'vencida'  => 'bg-rose-500 text-white',
                                            'rascunho' => 'bg-slate-500 text-white',
                                            default    => 'bg-gray-500 text-white',
                                        } ?>">
                                        <?= invoiceStatusLabel($invoice['status']) ?>
                                    </span>
                                    <p class="text-3xl font-black mt-3"><?= formatMoney((float)$invoice['total']) ?></p>
                                    <?php if ((float)$invoice['amount_due'] > 0): ?>
                                    <p class="text-rose-300 text-sm mt-1">
                                        Em dívida: <?= formatMoney((float)$invoice['amount_due']) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Client info -->
                        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Cliente</p>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                            <?= getInitials($invoice['client_name']) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($invoice['client_name']) ?></p>
                                            <?php if ($invoice['client_nuit']): ?>
                                            <p class="text-xs text-slate-500">NUIT: <?= htmlspecialchars($invoice['client_nuit']) ?></p>
                                            <?php endif; ?>
                                            <?php if ($invoice['client_email']): ?>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($invoice['client_email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Criada por</p>
                                    <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($invoice['created_by_name']) ?></p>
                                    <p class="text-xs text-slate-400"><?= formatDate($invoice['created_at'], 'd/m/Y H:i') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="px-8 py-5">
                            <h3 class="text-sm font-bold text-slate-700 mb-4">Itens da Fatura</h3>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="px-4 py-2 text-left rounded-l-lg">Descrição</th>
                                        <th class="px-4 py-2 text-center">Qtd.</th>
                                        <th class="px-4 py-2 text-right">Preço Unit.</th>
                                        <th class="px-4 py-2 text-right rounded-r-lg">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php foreach ($invoice['items'] as $item): ?>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($item['description']) ?></td>
                                        <td class="px-4 py-3 text-center text-slate-500"><?= number_format((float)$item['quantity'], 2) ?></td>
                                        <td class="px-4 py-3 text-right text-slate-500"><?= formatMoney((float)$item['unit_price']) ?></td>
                                        <td class="px-4 py-3 text-right font-bold text-slate-900"><?= formatMoney((float)$item['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Totals -->
                            <div class="mt-5 pt-5 border-t border-slate-100 max-w-xs ml-auto space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Subtotal</span>
                                    <span class="font-semibold"><?= formatMoney((float)$invoice['subtotal']) ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">IVA (<?= number_format((float)$invoice['iva_rate'], 0) ?>%)</span>
                                    <span class="font-semibold"><?= formatMoney((float)$invoice['iva_amount']) ?></span>
                                </div>
                                <?php if ((float)$invoice['discount'] > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Desconto</span>
                                    <span class="font-semibold text-rose-600">— <?= formatMoney((float)$invoice['discount']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ((float)$invoice['retencao_rate'] > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Retenção (<?= number_format((float)$invoice['retencao_rate'], 0) ?>%)</span>
                                    <span class="font-semibold text-rose-600">— <?= formatMoney((float)$invoice['retencao_amount']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between pt-3 border-t border-slate-900">
                                    <span class="font-black text-slate-900">TOTAL</span>
                                    <span class="font-black text-blue-600 text-lg"><?= formatMoney((float)$invoice['total']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <?php if ($invoice['notes'] || $invoice['terms']): ?>
                        <div class="px-8 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-slate-100 pt-5">
                            <?php if ($invoice['notes']): ?>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações</p>
                                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($invoice['notes'])) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($invoice['terms']): ?>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Termos de Pagamento</p>
                                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($invoice['terms'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Status + Payments -->
                <div class="space-y-5">

                    <!-- Change Status -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <h3 class="font-bold text-slate-900 mb-4 text-sm">Alterar Estado</h3>
                        <div class="space-y-2">
                            <?php
                            $allowedStatuses = [
                                'rascunho'  => ['Rascunho',  'slate'],
                                'emitida'   => ['Emitida',   'blue'],
                                'paga'      => ['Paga',      'emerald'],
                                'vencida'   => ['Vencida',   'rose'],
                                'cancelada' => ['Cancelada', 'gray'],
                            ];
                            foreach ($allowedStatuses as $val => [$label, $color]):
                                $isCurrent = $invoice['status'] === $val;
                            ?>
                            <button onclick="changeStatus('<?= $val ?>')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all
                                           <?= $isCurrent
                                               ? "bg-{$color}-100 text-{$color}-700 border-2 border-{$color}-300 cursor-default"
                                               : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"
                                    <?= $isCurrent ? 'disabled' : '' ?>>
                                <?php if ($isCurrent): ?>
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                <?php else: ?>
                                <span class="w-4 h-4 rounded-full border-2 border-slate-300 inline-block"></span>
                                <?php endif; ?>
                                <?= $label ?>
                                <?php if ($isCurrent): ?><span class="ml-auto text-[10px] uppercase font-black">Actual</span><?php endif; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Payments History -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-900 text-sm">Histórico de Pagamentos</h3>
                            <?php if ($invoice['status'] !== 'paga' && $invoice['status'] !== 'cancelada'): ?>
                            <button onclick="openPaymentModal()"
                                    class="text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">add</span> Registar
                            </button>
                            <?php endif; ?>
                        </div>

                        <!-- Progress bar -->
                        <?php
                        $total    = (float)$invoice['total'];
                        $paid     = (float)$invoice['amount_paid'];
                        $pct      = $total > 0 ? min(100, round(($paid / $total) * 100)) : 0;
                        ?>
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                                <span>Pago: <?= formatMoney($paid) ?></span>
                                <span><?= $pct ?>%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                                     style="width: <?= $pct ?>%"></div>
                            </div>
                            <?php if ((float)$invoice['amount_due'] > 0): ?>
                            <p class="text-xs text-rose-500 mt-1 font-semibold">Em dívida: <?= formatMoney((float)$invoice['amount_due']) ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($invoice['payments'])): ?>
                        <p class="text-sm text-slate-400 text-center py-4">Nenhum pagamento registado</p>
                        <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($invoice['payments'] as $pmt): ?>
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-emerald-600 text-[16px]">check</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-emerald-700"><?= formatMoney((float)$pmt['amount']) ?></span>
                                        <span class="text-[10px] text-slate-400"><?= formatDate($pmt['payment_date']) ?></span>
                                    </div>
                                    <p class="text-xs text-slate-500"><?= paymentMethodLabel($pmt['method']) ?></p>
                                    <?php if ($pmt['reference']): ?>
                                    <p class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($pmt['reference']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($pmt['receipt_number']): ?>
                                    <div class="flex items-center justify-between">
                                        <p class="text-[10px] text-slate-400">Recibo: <?= htmlspecialchars($pmt['receipt_number']) ?></p>
                                        <a href="/faturacao/recibo?id=<?= $pmt['id'] ?>" target="_blank"
                                           class="text-[10px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">print</span>
                                            Ver Recibo
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
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
                <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($invoice['invoice_number']) ?> — Em dívida: <?= formatMoney((float)$invoice['amount_due']) ?></p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="payment-form" class="p-6 space-y-4">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Valor (MT) *</label>
                <input type="number" name="amount" id="pay-amount" min="0.01" step="0.01"
                       value="<?= (float)$invoice['amount_due'] ?>" required
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Data *</label>
                <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
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
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Referência da Transação</label>
                <input type="text" name="reference" placeholder="Ex: TRF-BIM-20250501-001"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
            </div>
            <div id="pay-error" class="hidden px-4 py-3 bg-rose-50 text-rose-700 rounded-xl text-sm font-semibold"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closePaymentModal()"
                        class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all active:scale-95">
                    Confirmar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/faturacao';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openPaymentModal()  { document.getElementById('payment-modal').classList.replace('hidden','flex'); }
function closePaymentModal() { document.getElementById('payment-modal').classList.replace('flex','hidden'); }

document.getElementById('payment-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('pay-error');
    errDiv.classList.add('hidden');
    try {
        const fd  = new FormData(this);
        const res = await axios.post(`${BASE}/api/payments.php?action=store`, fd);
        if (res.data.success) {
            showToast(`Pagamento registado! Recibo: ${res.data.data.receipt_number}`);
            closePaymentModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            errDiv.textContent = res.data.message;
            errDiv.classList.remove('hidden');
        }
    } catch(e) {
        errDiv.textContent = 'Erro de comunicação.';
        errDiv.classList.remove('hidden');
    }
});

async function changeStatus(status) {
    try {
        const fd = new FormData();
        fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
        fd.append('status', status);
        const res = await axios.post(`${BASE}/api/invoices.php?action=change_status&id=<?= $invoice['id'] ?>`, fd);
        if (res.data.success) {
            showToast('Estado actualizado!');
            setTimeout(() => location.reload(), 700);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch(e) {
        showToast('Erro ao actualizar estado.', 'error');
    }
}



document.getElementById('payment-modal')?.addEventListener('click', e => {
    if (e.target === e.currentTarget) closePaymentModal();
});
</script>
</body>
</html>