<?php
/**
 * View: Lista de Pagamentos
 */
$pageTitle   = 'Pagamentos';
$currentPage = 'payments';
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
                    <h1 class="text-2xl font-black text-slate-900">Pagamentos</h1>
                    <p class="text-slate-500 text-sm mt-1"><?= number_format($total) ?> pagamentos registados</p>
                </div>
                <a href="/Sistema%20de%20Faturacao/faturas.php"
                   class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-emerald-600/20">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                    Registar Pagamento
                </a>
            </div>

            <!-- Stats cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <?php
                $methods = (new \PaymentModel())->getTotalsByMethod();
                $methodMap = [];
                foreach ($methods as $m) $methodMap[$m['method']] = $m;
                $totalReceived = array_sum(array_column($methods, 'total'));
                ?>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-600 text-[20px]">account_balance</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">Total Recebido</span>
                    </div>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney($totalReceived) ?></p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-[20px]">smartphone</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">Mobile Money</span>
                    </div>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($methodMap['mobile_money']['total'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)($methodMap['mobile_money']['count'] ?? 0) ?> transacções</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600 text-[20px]">swap_horiz</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-600">Transferência</span>
                    </div>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($methodMap['transferencia']['total'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)($methodMap['transferencia']['count'] ?? 0) ?> transacções</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Pesquisar por fatura, cliente ou referência..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white transition-all">
                    </div>
                    <select name="method" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos os métodos</option>
                        <option value="transferencia"  <?= ($method ?? '') === 'transferencia'  ? 'selected' : '' ?>>Transferência</option>
                        <option value="mobile_money"   <?= ($method ?? '') === 'mobile_money'   ? 'selected' : '' ?>>Mobile Money</option>
                        <option value="dinheiro"       <?= ($method ?? '') === 'dinheiro'       ? 'selected' : '' ?>>Dinheiro</option>
                        <option value="cheque"         <?= ($method ?? '') === 'cheque'         ? 'selected' : '' ?>>Cheque</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Filtrar
                    </button>
                    <?php if (!empty($search) || !empty($method)): ?>
                    <a href="/Sistema%20de%20Faturacao/pagamentos.php"
                       class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 flex items-center gap-1">
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
                            <th class="px-6 py-3 text-left">Recibo</th>
                            <th class="px-6 py-3 text-left">Fatura</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Data</th>
                            <th class="px-6 py-3 text-left">Método</th>
                            <th class="px-6 py-3 text-left">Referência</th>
                            <th class="px-6 py-3 text-right">Valor</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $pmt): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                                    <?= htmlspecialchars($pmt['receipt_number'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="/Sistema%20de%20Faturacao/faturas.php?id=<?= $pmt['invoice_id'] ?>"
                                   class="font-mono text-xs text-blue-600 hover:underline font-semibold">
                                    <?= htmlspecialchars($pmt['invoice_number']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">
                                        <?= getInitials($pmt['client_name']) ?>
                                    </div>
                                    <span class="font-semibold text-slate-800 text-xs"><?= htmlspecialchars($pmt['client_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= formatDate($pmt['payment_date']) ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $mc = match($pmt['method']) {
                                    'transferencia' => ['bg-blue-100 text-blue-700',   'swap_horiz',   'Transferência'],
                                    'mobile_money'  => ['bg-purple-100 text-purple-700','smartphone',  'Mobile Money'],
                                    'dinheiro'      => ['bg-emerald-100 text-emerald-700','payments',  'Dinheiro'],
                                    'cheque'        => ['bg-amber-100 text-amber-700',  'article',     'Cheque'],
                                    default         => ['bg-slate-100 text-slate-600',  'more_horiz',  'Outro'],
                                };
                                ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?= $mc[0] ?>">
                                    <span class="material-symbols-outlined text-[12px]"><?= $mc[1] ?></span>
                                    <?= $mc[2] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-xs font-mono"><?= htmlspecialchars($pmt['reference'] ?? '—') ?></td>
                            <td class="px-6 py-4 text-right font-black text-emerald-600 text-base">
                                <?= formatMoney((float)$pmt['amount']) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/Sistema%20de%20Faturacao/recibo.php?id=<?= $pmt['id'] ?>" target="_blank"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                       title="Imprimir Recibo">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <button onclick="deletePayment(<?= $pmt['id'] ?>, '<?= htmlspecialchars(addslashes($pmt['receipt_number'] ?? '')) ?>')"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all"
                                            title="Anular Pagamento">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr><td colspan="8" class="px-6 py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">payments</span>
                            Nenhum pagamento encontrado.
                            <a href="/Sistema%20de%20Faturacao/faturas.php" class="block mt-2 text-blue-600 font-semibold text-sm hover:underline">
                                Ir para Faturas para registar um pagamento →
                            </a>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($last_page > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-sm">
                    <p class="text-slate-500">Mostrando <?= count($items) ?> de <?= $total ?> pagamentos</p>
                    <div class="flex gap-1">
                        <?php for ($p = 1; $p <= min($last_page, 10); $p++): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&method=<?= urlencode($method ?? '') ?>"
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

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
<script>
const BASE = '/Sistema%20de%20Faturacao';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function deletePayment(id, receipt) {
    confirmDelete(`Anular o pagamento "${receipt}"? O saldo da fatura será reajustado.`, async () => {
        try {
            const fd = new FormData();
            fd.append('<?= CSRF_TOKEN_NAME ?>', CSRF);
            const res = await axios.post(`${BASE}/api/payments.php?id=${id}&action=delete`, fd);
            if (res.data.success) {
                showToast('Pagamento anulado.');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(res.data.message, 'error');
            }
        } catch(e) {
            showToast('Erro ao anular pagamento.', 'error');
        }
    });
}
</script>
</body>
</html>
