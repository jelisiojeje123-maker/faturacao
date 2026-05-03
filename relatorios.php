<?php
/**
 * Controller + View: Relatórios
 * Integrado num único ficheiro para simplicidade
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/InvoiceModel.php';
require_once __DIR__ . '/models/PaymentModel.php';

requireAuth();

$invoiceModel = new InvoiceModel();
$paymentModel = new PaymentModel();

// Período por defeito: mês actual
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo   = $_GET['date_to']   ?? date('Y-m-t');
$dateFrom = sanitize($dateFrom);
$dateTo   = sanitize($dateTo);

// Exportar CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $rows = $invoiceModel->getReport($dateFrom, $dateTo);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_' . $dateFrom . '_' . $dateTo . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    fputcsv($out, ['Nº Fatura','Cliente','Emissão','Vencimento','Estado','Subtotal','IVA','Total','Pago','Em Dívida'], ';');
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['invoice_number'], $r['client_name'],
            $r['issue_date'], $r['due_date'], invoiceStatusLabel($r['status']),
            number_format((float)$r['subtotal'],2,',','.'),
            number_format((float)$r['iva_amount'],2,',','.'),
            number_format((float)$r['total'],2,',','.'),
            number_format((float)$r['amount_paid'],2,',','.'),
            number_format((float)$r['amount_due'],2,',','.'),
        ], ';');
    }
    fclose($out);
    exit;
}

$summary  = $invoiceModel->getReportSummary($dateFrom, $dateTo);
$invoices = $invoiceModel->getReport($dateFrom, $dateTo);
$payByMethod = $paymentModel->getTotalsByMethod($dateFrom, $dateTo);

// Chart data por estado
$statusChart = [
    'labels' => ['Pagas', 'Emitidas', 'Vencidas', 'Rascunho'],
    'data'   => [
        (int)($summary['count_paid'] ?? 0),
        (int)($summary['count_pending'] ?? 0),
        (int)($summary['count_overdue'] ?? 0),
        (int)($summary['total_invoices'] ?? 0) - (int)($summary['count_paid'] ?? 0) - (int)($summary['count_pending'] ?? 0) - (int)($summary['count_overdue'] ?? 0),
    ],
    'colors' => ['#10b981','#3b82f6','#ef4444','#94a3b8'],
];

$pageTitle   = 'Relatórios';
$currentPage = 'reports';

// Versão de Impressão
if (isset($_GET['print'])) {
    $company = Database::getInstance()->query("SELECT * FROM company_settings LIMIT 1")->fetch();
    require_once __DIR__ . '/views/reports/print.php';
    exit;
}

require_once __DIR__ . '/../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
<style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        body { background: white !important; }
        .bg-white { border: 1px solid #eee !important; shadow: none !important; }
    }
    .print-only { display: none; }
</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<div class="flex">
    <?php require_once __DIR__ . '/../views/partials/sidebar.php'; ?>
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Relatórios</h1>
                    <p class="text-slate-500 text-sm mt-1">Análise financeira por período</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="printReport()"
                            class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 font-semibold px-5 py-2.5 rounded-xl transition-all hover:bg-slate-50 text-sm shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Imprimir / PDF
                    </button>
                    <a href="?date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>&export=csv"
                       class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-emerald-600/20">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Exportar CSV
                    </a>
                </div>
            </div>

            <!-- Period Filter -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Data Inicial</label>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Data Final</label>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                    </div>
                    <!-- Quick shortcuts -->
                    <div class="flex gap-2 flex-wrap">
                        <?php
                        $shortcuts = [
                            'Este Mês'  => [date('Y-m-01'), date('Y-m-t')],
                            'Mês Ant.'  => [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
                            'Este Ano'  => [date('Y-01-01'), date('Y-12-31')],
                        ];
                        foreach ($shortcuts as $label => [$f, $t]): ?>
                        <a href="?date_from=<?= $f ?>&date_to=<?= $t ?>"
                           class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all whitespace-nowrap
                                  <?= ($dateFrom === $f && $dateTo === $t) ? 'bg-blue-600 text-white border-blue-600' : '' ?>">
                            <?= $label ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Aplicar
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Faturado</p>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($summary['total_billed'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)($summary['total_invoices'] ?? 0) ?> faturas</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Recebido</p>
                    <p class="text-2xl font-black text-emerald-600"><?= formatMoney((float)($summary['total_received'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)($summary['count_paid'] ?? 0) ?> pagas</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total IVA</p>
                    <p class="text-2xl font-black text-blue-600"><?= formatMoney((float)($summary['total_iva'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1">IVA a entregar ao Estado</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Em Dívida</p>
                    <p class="text-2xl font-black text-rose-600"><?= formatMoney((float)($summary['total_due'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)(($summary['count_pending'] ?? 0) + ($summary['count_overdue'] ?? 0)) ?> faturas por cobrar</p>
                </div>
            </div>

            <!-- Charts + Methods -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pie Chart: Status -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col items-center">
                    <h3 class="font-bold text-slate-900 mb-4 self-start">Faturas por Estado</h3>
                    <div class="w-56 h-56">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2 self-start w-full">
                        <?php foreach (['Pagas','Emitidas','Vencidas','Rascunho'] as $i => $lbl): ?>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background:<?= $statusChart['colors'][$i] ?>"></div>
                                <span class="text-slate-600"><?= $lbl ?></span>
                            </div>
                            <span class="font-bold text-slate-900"><?= $statusChart['data'][$i] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Payment methods breakdown -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-5">Pagamentos por Método</h3>
                    <?php if (empty($payByMethod)): ?>
                    <div class="flex flex-col items-center justify-center h-40 text-slate-300">
                        <span class="material-symbols-outlined text-4xl mb-2">payments</span>
                        <p class="text-sm">Sem pagamentos neste período</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php
                        $totalPay = array_sum(array_column($payByMethod, 'total'));
                        $methodIcons = ['transferencia'=>'swap_horiz','mobile_money'=>'smartphone','dinheiro'=>'payments','cheque'=>'article','outro'=>'more_horiz'];
                        $methodColors = ['transferencia'=>'blue','mobile_money'=>'purple','dinheiro'=>'emerald','cheque'=>'amber','outro'=>'slate'];
                        foreach ($payByMethod as $pm):
                            $pct = $totalPay > 0 ? round(($pm['total'] / $totalPay) * 100) : 0;
                            $col = $methodColors[$pm['method']] ?? 'slate';
                            $ico = $methodIcons[$pm['method']] ?? 'more_horiz';
                        ?>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-<?= $col ?>-600 text-[18px]"><?= $ico ?></span>
                                    <span class="text-sm font-semibold text-slate-700"><?= paymentMethodLabel($pm['method']) ?></span>
                                    <span class="text-xs text-slate-400"><?= (int)$pm['count'] ?> transacções</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-900"><?= formatMoney((float)$pm['total']) ?></span>
                                    <span class="text-xs text-slate-400 ml-1">(<?= $pct ?>%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-<?= $col ?>-500 h-2 rounded-full" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Invoice Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">
                        Faturas do Período
                        <span class="ml-2 text-sm font-medium text-slate-400">(<?= formatDate($dateFrom) ?> — <?= formatDate($dateTo) ?>)</span>
                    </h3>
                    <span class="text-sm text-slate-500"><?= count($invoices) ?> registos</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-3 text-left">Fatura</th>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Emissão</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                                <th class="px-6 py-3 text-right">IVA</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="/Sistema%20de%20Faturacao/faturas.php?id=<?= $inv['id'] ?>"
                                       class="font-mono text-xs text-blue-600 hover:underline font-semibold">
                                        <?= htmlspecialchars($inv['invoice_number']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-slate-700 font-medium"><?= htmlspecialchars($inv['client_name']) ?></td>
                                <td class="px-6 py-3 text-slate-500"><?= formatDate($inv['issue_date']) ?></td>
                                <td class="px-6 py-3 text-right text-slate-600"><?= formatMoney((float)$inv['subtotal']) ?></td>
                                <td class="px-6 py-3 text-right text-blue-600"><?= formatMoney((float)$inv['iva_amount']) ?></td>
                                <td class="px-6 py-3 text-right font-bold text-slate-900"><?= formatMoney((float)$inv['total']) ?></td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= invoiceStatusClass($inv['status']) ?>">
                                        <?= invoiceStatusLabel($inv['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($invoices)): ?>
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Nenhuma fatura neste período.
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($invoices)): ?>
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-black text-sm">
                                <td class="px-6 py-3" colspan="3">TOTAL DO PERÍODO</td>
                                <td class="px-6 py-3 text-right"><?= formatMoney((float)($summary['total_billed'] ?? 0) - (float)($summary['total_iva'] ?? 0)) ?></td>
                                <td class="px-6 py-3 text-right text-blue-300"><?= formatMoney((float)($summary['total_iva'] ?? 0)) ?></td>
                                <td class="px-6 py-3 text-right text-emerald-300"><?= formatMoney((float)($summary['total_billed'] ?? 0)) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>
<script>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($statusChart['labels']) ?>,
        datasets: [{
            data:            <?= json_encode($statusChart['data']) ?>,
            backgroundColor: <?= json_encode($statusChart['colors']) ?>,
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
        }
    }
});

function printReport() {
    const url = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'print=1';
    const win = window.open(url, '_blank');
    win.focus();
}
</script>
</body>
</html>
