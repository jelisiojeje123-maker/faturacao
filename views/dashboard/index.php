<?php
/**
 * View: Dashboard
 */
$pageTitle  = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../../views/partials/head.php';
?>
<meta name="csrf-token" content="<?= generateCsrfToken() ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

<div class="flex">
    <?php require_once __DIR__ . '/../../views/partials/sidebar.php'; ?>

    <!-- Main -->
    <main class="md:ml-[260px] flex-1 flex flex-col min-h-screen">
        <?php require_once __DIR__ . '/../../views/partials/topbar.php'; ?>

        <div class="p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-8 flex-1">

            <!-- Welcome -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">
                        Bom dia, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?>! 👋
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        <?= date('l, d \d\e F \d\e Y', strtotime('now')) ?> — Resumo financeiro do sistema
                    </p>
                </div>
                    Nova Fatura
                </a>
            </div>

            <?php if ($pendingQuotes > 0): ?>
            <div class="bg-blue-600 rounded-2xl p-4 text-white flex items-center justify-between shadow-lg shadow-blue-600/20">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">request_quote</span>
                    </div>
                    <div>
                        <p class="font-bold">Tem <?= $pendingQuotes ?> orçamentos pendentes</p>
                        <p class="text-xs text-blue-100">Alguns clientes estão à espera da sua proposta comercial.</p>
                    </div>
                </div>
                <a href="/faturacao/orcamentos?status=enviado" 
                   class="bg-white text-blue-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-50 transition-colors">
                    Ver Orçamentos
                </a>
            </div>
            <?php endif; ?>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Total Faturado -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-[22px]">account_balance</span>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Este ano</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Faturado</p>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($kpis['total_billed'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1"><?= (int)($kpis['total_invoices'] ?? 0) ?> faturas emitidas</p>
                </div>

                <!-- Total Recebido -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Pago</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Recebido</p>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($kpis['total_received'] ?? 0)) ?></p>
                    <p class="text-xs text-emerald-600 mt-1 font-semibold">
                        <?php
                        $billed   = (float)($kpis['total_billed'] ?? 0);
                        $received = (float)($kpis['total_received'] ?? 0);
                        $pct = $billed > 0 ? round(($received / $billed) * 100, 1) : 0;
                        ?>
                        <?= $pct ?>% do total faturado
                    </p>
                </div>

                <!-- Pendentes -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-600 text-[22px]">schedule</span>
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full"><?= (int)($kpis['count_pending'] ?? 0) ?> faturas</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Em Aberto</p>
                    <p class="text-2xl font-black text-slate-900"><?= formatMoney((float)($kpis['pending'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1">A aguardar pagamento</p>
                </div>

                <!-- Vencidas -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 bg-rose-50 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-rose-600 text-[22px]">error_outline</span>
                        </div>
                        <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full"><?= (int)($kpis['count_overdue'] ?? 0) ?> faturas</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Vencidas</p>
                    <p class="text-2xl font-black text-rose-600"><?= formatMoney((float)($kpis['overdue'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-400 mt-1">Requer atenção urgente</p>
                </div>
            </div>

            <!-- Chart + Due Soon -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Evolução Mensal</h3>
                            <p class="text-xs text-slate-500">Faturado vs Recebido — últimos 12 meses</p>
                        </div>
                    </div>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>

                <!-- Due Soon -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Próximos Vencimentos</h3>
                    <p class="text-xs text-slate-500 mb-5">Próximos 7 dias</p>

                    <?php if (empty($dueSoon)): ?>
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                            <span class="material-symbols-outlined text-4xl text-emerald-400 mb-2">check_circle</span>
                            <p class="text-sm text-slate-500">Sem vencimentos próximos</p>
                        </div>
                    <?php else: ?>
                    <div class="space-y-3 flex-1">
                        <?php foreach ($dueSoon as $due): ?>
                        <a href="/faturacao/faturas?id=<?= $due['id'] ?>"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">
                                <?= getInitials($due['client_name']) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($due['client_name']) ?></p>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($due['invoice_number']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold <?= $due['status'] === 'vencida' ? 'text-rose-600' : 'text-amber-600' ?>">
                                    <?= formatMoney((float)$due['amount_due']) ?>
                                </p>
                                <p class="text-[10px] text-slate-400"><?= formatDate($due['due_date']) ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <a href="/faturacao/faturas"
                       class="mt-4 text-center text-xs font-bold text-blue-600 hover:underline">
                        Ver todas as faturas →
                    </a>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Faturas Recentes</h3>
                    <a href="/faturacao/faturas" class="text-xs font-semibold text-blue-600 hover:underline">Ver todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Nº Fatura</th>
                                <th class="px-6 py-3 text-left">Data</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($recentInvoices as $inv): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                                            <?= getInitials($inv['client_name']) ?>
                                        </div>
                                        <span class="font-semibold text-slate-800"><?= htmlspecialchars($inv['client_name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                                <td class="px-6 py-4 text-slate-500"><?= formatDate($inv['issue_date']) ?></td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900"><?= formatMoney((float)$inv['total']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= invoiceStatusClass($inv['status']) ?>">
                                        <?= invoiceStatusLabel($inv['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/faturacao/faturas?id=<?= $inv['id'] ?>"
                                       class="text-slate-400 hover:text-blue-600 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentInvoices)): ?>
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">Nenhuma fatura ainda. <a href="/faturacao/criar-fatura" class="text-blue-600 font-semibold">Criar a primeira →</a></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>

<script>
// Revenue Chart
const chartData = <?= json_encode($chartData) ?>;
const labels  = chartData.map(d => d.month_label);
const billed  = chartData.map(d => parseFloat(d.billed));
const received = chartData.map(d => parseFloat(d.received));

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Faturado',
                data: billed,
                backgroundColor: 'rgba(37,99,235,0.15)',
                borderColor:     'rgba(37,99,235,0.8)',
                borderWidth: 2,
                borderRadius: 6,
                order: 2,
            },
            {
                label: 'Recebido',
                data: received,
                type: 'line',
                borderColor:     'rgba(16,185,129,0.9)',
                backgroundColor: 'rgba(16,185,129,0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
                order: 1,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Inter', size: 11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ' MT ' + ctx.parsed.y.toLocaleString('pt-MZ', {minimumFractionDigits:2})
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family:'Inter', size:11 } } },
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    font: { family:'Inter', size:11 },
                    callback: v => 'MT ' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
                }
            }
        }
    }
});
</script>
</body>
</html>
