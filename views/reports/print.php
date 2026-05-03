<?php
/**
 * View: Impressão de Relatório Financeiro
 */
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro (<?= formatDate($dateFrom) ?> - <?= formatDate($dateTo) ?>)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .print-container { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 p-4 sm:p-8">

    <div class="max-w-[1200px] mx-auto bg-white p-8 shadow-sm rounded-sm print-container">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 border-b border-slate-100 pb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 mb-1"><?= htmlspecialchars($company['name']) ?></h1>
                <p class="text-sm text-slate-500 uppercase tracking-widest font-bold">Relatório Financeiro</p>
                <p class="text-xs text-slate-400 mt-2">Período: <?= formatDate($dateFrom) ?> até <?= formatDate($dateTo) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase font-bold mb-1">Gerado em</p>
                <p class="text-sm font-bold text-slate-900"><?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="border border-slate-100 p-4 rounded-xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Faturado</p>
                <p class="text-lg font-black text-slate-900"><?= formatMoney((float)($summary['total_billed'] ?? 0)) ?></p>
            </div>
            <div class="border border-slate-100 p-4 rounded-xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Recebido</p>
                <p class="text-lg font-black text-emerald-600"><?= formatMoney((float)($summary['total_received'] ?? 0)) ?></p>
            </div>
            <div class="border border-slate-100 p-4 rounded-xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total IVA</p>
                <p class="text-lg font-black text-blue-600"><?= formatMoney((float)($summary['total_iva'] ?? 0)) ?></p>
            </div>
            <div class="border border-slate-100 p-4 rounded-xl">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Em Dívida</p>
                <p class="text-lg font-black text-rose-600"><?= formatMoney((float)($summary['total_due'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- Methods Summary -->
        <div class="mb-8">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">Pagamentos por Método</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($payByMethod as $pm): ?>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1"><?= paymentMethodLabel($pm['method']) ?></p>
                    <p class="text-sm font-black text-slate-900"><?= formatMoney((float)$pm['total']) ?></p>
                    <p class="text-[9px] text-slate-400 mt-1"><?= (int)$pm['count'] ?> transacções</p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Table -->
        <div>
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">Detalhamento das Faturas</h3>
            <table class="w-full text-[11px]">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider">
                        <th class="px-3 py-2 text-left">Nº Fatura</th>
                        <th class="px-3 py-2 text-left">Cliente</th>
                        <th class="px-3 py-2 text-left">Data</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                        <th class="px-3 py-2 text-right">IVA</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td class="px-3 py-2 font-mono font-bold"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                        <td class="px-3 py-2"><?= htmlspecialchars($inv['client_name']) ?></td>
                        <td class="px-3 py-2"><?= formatDate($inv['issue_date']) ?></td>
                        <td class="px-3 py-2 text-right"><?= formatMoney((float)$inv['subtotal']) ?></td>
                        <td class="px-3 py-2 text-right"><?= formatMoney((float)$inv['iva_amount']) ?></td>
                        <td class="px-3 py-2 text-right font-bold"><?= formatMoney((float)$inv['total']) ?></td>
                        <td class="px-3 py-2 text-center uppercase font-black text-[9px]">
                            <?= invoiceStatusLabel($inv['status']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-900">
                    <tr class="font-black text-sm">
                        <td class="px-3 py-4" colspan="3">TOTAL GERAL</td>
                        <td class="px-3 py-4 text-right"><?= formatMoney((float)($summary['total_billed'] ?? 0) - (float)($summary['total_iva'] ?? 0)) ?></td>
                        <td class="px-3 py-4 text-right"><?= formatMoney((float)($summary['total_iva'] ?? 0)) ?></td>
                        <td class="px-3 py-4 text-right text-blue-600"><?= formatMoney((float)($summary['total_billed'] ?? 0)) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-12 mt-16 no-print">
            <div class="text-center pt-8 border-t border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Responsável Financeiro</p>
            </div>
            <div class="text-center pt-8 border-t border-slate-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Direcção Geral</p>
            </div>
        </div>
    </div>

    <!-- Floating Actions -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 no-print">
        <button onclick="window.close()" class="bg-white border border-slate-200 px-6 py-3 rounded-xl font-bold shadow-lg text-sm hover:bg-slate-50 transition-all">
            Fechar
        </button>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all active:scale-95">
            <span class="material-symbols-outlined text-[20px]">print</span>
            Imprimir Agora
        </button>
    </div>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</body>
</html>
