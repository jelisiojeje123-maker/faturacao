<?php
/**
 * View: Detalhe do Orçamento (show)
 */
$pageTitle   = 'Orçamento ' . $quote['quote_number'];
$currentPage = 'quotes';
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
                    <a href="/Sistema%20de%20Faturacao/orcamentos.php" class="hover:text-blue-600 transition-colors">Orçamentos</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="font-semibold text-slate-900 font-mono"><?= htmlspecialchars($quote['quote_number']) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/Sistema%20de%20Faturacao/orcamentos.php?action=print&id=<?= $quote['id'] ?>" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 border border-slate-200 bg-white text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Imprimir / PDF
                    </a>
                    <?php if ($quote['status'] === 'aceite'): ?>
                    <button onclick="convertToInvoice()"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all active:scale-95 shadow-lg shadow-blue-600/20">
                        <span class="material-symbols-outlined text-[18px]">transform</span>
                        Converter para Fatura
                    </button>
                    <?php endif; ?>
                    <?php if ($quote['status'] === 'enviado'): ?>
                    <button onclick="changeStatus('aceite')"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Aceitar Orçamento
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Quote Main Content -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Quote Header Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="bg-slate-900 px-8 py-6 text-white">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Orçamento</p>
                                    <h2 class="text-2xl font-black font-mono"><?= htmlspecialchars($quote['quote_number']) ?></h2>
                                    <p class="text-slate-400 text-sm mt-2">
                                        Emitido em <?= formatDate($quote['issue_date']) ?>
                                        <?php if ($quote['expiry_date']): ?>
                                        &bull; Válido até <?= formatDate($quote['expiry_date']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <?php
                                    $statusClass = match($quote['status']) {
                                        'rascunho'   => 'bg-slate-500 text-white',
                                        'enviado'    => 'bg-blue-500 text-white',
                                        'aceite'     => 'bg-emerald-500 text-white',
                                        'recusado'   => 'bg-rose-500 text-white',
                                        'expirado'   => 'bg-orange-500 text-white',
                                        'convertido' => 'bg-purple-500 text-white',
                                        default      => 'bg-gray-500 text-white',
                                    };
                                    ?>
                                    <span class="inline-block px-3 py-1.5 rounded-full text-sm font-black uppercase <?= $statusClass ?>">
                                        <?= htmlspecialchars($quote['status']) ?>
                                    </span>
                                    <p class="text-3xl font-black mt-3"><?= formatMoney((float)$quote['total']) ?></p>
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
                                            <?= getInitials($quote['client_name']) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($quote['client_name']) ?></p>
                                            <?php if ($quote['client_nuit']): ?>
                                            <p class="text-xs text-slate-500">NUIT: <?= htmlspecialchars($quote['client_nuit']) ?></p>
                                            <?php endif; ?>
                                            <?php if ($quote['client_email']): ?>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($quote['client_email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Criado por</p>
                                    <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($quote['created_by_name']) ?></p>
                                    <p class="text-xs text-slate-400"><?= formatDate($quote['created_at'], 'd/m/Y H:i') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="px-8 py-5">
                            <h3 class="text-sm font-bold text-slate-700 mb-4">Itens do Orçamento</h3>
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
                                    <?php foreach ($quote['items'] as $item): ?>
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
                                    <span class="font-semibold"><?= formatMoney((float)$quote['subtotal']) ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">IVA (16%)</span>
                                    <span class="font-semibold"><?= formatMoney((float)$quote['iva_amount']) ?></span>
                                </div>
                                <?php if ((float)$quote['discount'] > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Desconto</span>
                                    <span class="font-semibold text-rose-600">— <?= formatMoney((float)$quote['discount']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between pt-3 border-t border-slate-900">
                                    <span class="font-black text-slate-900">TOTAL ESTIMADO</span>
                                    <span class="font-black text-blue-600 text-lg"><?= formatMoney((float)$quote['total']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <?php if ($quote['notes'] || $quote['terms']): ?>
                        <div class="px-8 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-slate-100 pt-5">
                            <?php if ($quote['notes']): ?>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações</p>
                                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($quote['notes'])) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($quote['terms']): ?>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Termos e Condições</p>
                                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($quote['terms'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Status -->
                <div class="space-y-5">

                    <!-- Change Status -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <h3 class="font-bold text-slate-900 mb-4 text-sm">Alterar Estado</h3>
                        <div class="space-y-2">
                            <?php
                            $allowedStatuses = [
                                'rascunho'   => ['Rascunho',   'slate'],
                                'enviado'    => ['Enviado',    'blue'],
                                'aceite'     => ['Aceite',     'emerald'],
                                'recusado'   => ['Recusado',   'rose'],
                                'expirado'   => ['Expirado',   'orange'],
                                'convertido' => ['Convertido', 'purple'],
                            ];
                            foreach ($allowedStatuses as $val => [$label, $color]):
                                $isCurrent = $quote['status'] === $val;
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
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/Sistema%20de%20Faturacao';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function changeStatus(status) {
    try {
        const fd = new FormData();
        fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
        fd.append('status', status);
        const res = await axios.post(`${BASE}/api/quotes.php?action=change_status&id=<?= $quote['id'] ?>`, fd);
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

async function convertToInvoice() {
    if (!confirm('Deseja converter este orçamento numa fatura emitida?')) return;
    try {
        const fd = new FormData();
        fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
        const res = await axios.post(`${BASE}/api/quotes.php?action=convert&id=<?= $quote['id'] ?>`, fd);
        if (res.data.success) {
            showToast('Orçamento convertido com sucesso!');
            setTimeout(() => window.location.href = `${BASE}/faturas.php?id=${res.data.data.invoice_id}`, 1500);
        } else {
            showToast(res.data.message, 'error');
        }
    } catch(e) {
        showToast('Erro ao converter orçamento.', 'error');
    }
}
</script>
</body>
</html>
