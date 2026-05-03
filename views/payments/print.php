<?php
/**
 * View: Impressão de Recibo
 */
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo <?= htmlspecialchars($payment['receipt_number']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-border { border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8">

    <div class="max-w-[800px] mx-auto bg-white p-8 shadow-sm print-border rounded-sm">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-3xl font-black text-slate-900 mb-2"><?= htmlspecialchars($company['name']) ?></h1>
                <div class="text-sm text-slate-500 space-y-1">
                    <p>NUIT: <?= htmlspecialchars($company['nuit']) ?></p>
                    <p><?= htmlspecialchars($company['address']) ?></p>
                    <p><?= htmlspecialchars($company['city']) ?>, Moçambique</p>
                    <p>Tel: <?= htmlspecialchars($company['phone']) ?></p>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-slate-900 text-white px-6 py-3 rounded-lg inline-block mb-4">
                    <h2 class="text-xl font-bold uppercase tracking-widest">RECIBO</h2>
                </div>
                <p class="text-sm font-bold text-slate-900">Nº <?= htmlspecialchars($payment['receipt_number']) ?></p>
                <p class="text-xs text-slate-500">Data: <?= formatDate($payment['payment_date']) ?></p>
            </div>
        </div>

        <!-- Receipt Content -->
        <div class="space-y-8">
            <div class="border-y border-slate-100 py-8">
                <p class="text-lg leading-loose text-slate-700">
                    Recebemos de <span class="font-black text-slate-900"><?= htmlspecialchars($payment['client_name']) ?></span>, 
                    com o NUIT <span class="font-bold text-slate-900"><?= htmlspecialchars($payment['client_nuit']) ?></span>, 
                    a quantia de <span class="text-2xl font-black text-emerald-600"><?= formatMoney((float)$payment['amount']) ?></span> 
                    (<?= htmlspecialchars($company['currency']) ?>), referente ao pagamento da 
                    <span class="font-bold text-slate-900">Fatura <?= htmlspecialchars($payment['invoice_number']) ?></span>.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Detalhes do Pagamento</h3>
                    <table class="w-full text-sm">
                        <tr class="border-b border-slate-50">
                            <td class="py-2 text-slate-500">Método:</td>
                            <td class="py-2 font-semibold text-slate-900"><?= paymentMethodLabel($payment['method']) ?></td>
                        </tr>
                        <?php if ($payment['reference']): ?>
                        <tr class="border-b border-slate-50">
                            <td class="py-2 text-slate-500">Referência:</td>
                            <td class="py-2 font-mono text-slate-900"><?= htmlspecialchars($payment['reference']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="bg-slate-50 p-6 rounded-2xl flex flex-col items-center justify-center border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Valor Total</p>
                    <p class="text-3xl font-black text-slate-900"><?= formatMoney((float)$payment['amount']) ?></p>
                </div>
            </div>

            <?php if ($payment['notes']): ?>
            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                <h4 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">Observações</h4>
                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($payment['notes'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Footer Signatures -->
            <div class="grid grid-cols-2 gap-12 pt-12">
                <div class="text-center">
                    <div class="border-b border-slate-300 mb-2 h-12"></div>
                    <p class="text-[10px] text-slate-400 uppercase font-bold">O Cliente</p>
                </div>
                <div class="text-center">
                    <div class="border-b border-slate-300 mb-2 h-12 flex items-end justify-center">
                        <span class="text-slate-400 italic text-sm mb-1"><?= htmlspecialchars($payment['received_by']) ?></span>
                    </div>
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Processado por (A Empresa)</p>
                </div>
            </div>
        </div>

        <div class="mt-16 text-center text-[10px] text-slate-300 uppercase tracking-[0.2em]">
            Obrigado pela sua preferência
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-[800px] mx-auto mt-8 flex justify-between no-print">
        <a href="/faturacao/pagamentos.php" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-semibold transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
            Voltar à lista
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all active:scale-95">
            <span class="material-symbols-outlined">print</span>
            Imprimir Recibo
        </button>
    </div>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</body>
</html>
