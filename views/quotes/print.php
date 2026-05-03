<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Orçamento <?= htmlspecialchars($quote['quote_number']) ?> — <?= htmlspecialchars($company['name'] ?? 'FaturaMZ') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <style>
        * { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .quote-card { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

    <!-- Print Actions -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex gap-3 justify-end">
        <button onclick="window.print()"
                class="flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition-all">
            🖨 Imprimir / Guardar PDF
        </button>
        <a href="javascript:window.close()"
           class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-all">
            ✕ Fechar
        </a>
    </div>

    <!-- Quote Card -->
    <div class="quote-card max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-slate-900 px-10 py-8 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-black mb-1"><?= htmlspecialchars($company['name'] ?? 'Empresa') ?></h1>
                    <?php if ($company['nuit']): ?>
                    <p class="text-slate-400 text-sm">NUIT: <?= htmlspecialchars($company['nuit']) ?></p>
                    <?php endif; ?>
                    <?php if ($company['address']): ?>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($company['address']) ?></p>
                    <?php endif; ?>
                    <?php if ($company['phone']): ?>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($company['phone']) ?></p>
                    <?php endif; ?>
                    <?php if ($company['email']): ?>
                    <p class="text-slate-400 text-sm"><?= htmlspecialchars($company['email']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-black text-blue-400 mb-2">ORÇAMENTO</div>
                    <div class="font-mono text-lg font-bold"><?= htmlspecialchars($quote['quote_number']) ?></div>
                    <div class="mt-4 space-y-1 text-sm">
                        <div class="flex items-center gap-3 justify-end">
                            <span class="text-slate-400">Emissão:</span>
                            <span class="font-semibold"><?= formatDate($quote['issue_date']) ?></span>
                        </div>
                        <?php if ($quote['expiry_date']): ?>
                        <div class="flex items-center gap-3 justify-end">
                            <span class="text-slate-400">Validade:</span>
                            <span class="font-semibold"><?= formatDate($quote['expiry_date']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- Status badge -->
                    <div class="mt-3">
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase
                            <?= match($quote['status']) {
                                'aceite'     => 'bg-emerald-500 text-white',
                                'enviado'    => 'bg-blue-500 text-white',
                                'recusado'   => 'bg-rose-500 text-white',
                                default      => 'bg-slate-500 text-white',
                            } ?>">
                            <?= htmlspecialchars($quote['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Info -->
        <div class="px-10 py-6 bg-slate-50 border-b border-slate-200">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Orçamento para:</p>
                    <p class="font-bold text-slate-900 text-base"><?= htmlspecialchars($quote['client_name']) ?></p>
                    <?php if ($quote['client_nuit']): ?>
                    <p class="text-slate-500 text-sm">NUIT: <?= htmlspecialchars($quote['client_nuit']) ?></p>
                    <?php endif; ?>
                    <?php if ($quote['client_email']): ?>
                    <p class="text-slate-500 text-sm"><?= htmlspecialchars($quote['client_email']) ?></p>
                    <?php endif; ?>
                    <?php if ($quote['client_phone']): ?>
                    <p class="text-slate-500 text-sm"><?= htmlspecialchars($quote['client_phone']) ?></p>
                    <?php endif; ?>
                    <?php if ($quote['client_address']): ?>
                    <p class="text-slate-500 text-sm"><?= htmlspecialchars($quote['client_address']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="px-10 py-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-900">
                        <th class="text-left pb-3 text-xs font-black uppercase tracking-wider text-slate-600">Descrição</th>
                        <th class="text-center pb-3 text-xs font-black uppercase tracking-wider text-slate-600 w-20">Qtd.</th>
                        <th class="text-right pb-3 text-xs font-black uppercase tracking-wider text-slate-600 w-32">Preço Unit.</th>
                        <th class="text-right pb-3 text-xs font-black uppercase tracking-wider text-slate-600 w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($quote['items'] as $item): ?>
                    <tr>
                        <td class="py-3 text-slate-800 font-medium"><?= htmlspecialchars($item['description']) ?></td>
                        <td class="py-3 text-center text-slate-600"><?= number_format((float)$item['quantity'], 2) ?></td>
                        <td class="py-3 text-right text-slate-600"><?= formatMoney((float)$item['unit_price']) ?></td>
                        <td class="py-3 text-right font-bold text-slate-900"><?= formatMoney((float)$item['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="px-10 pb-8">
            <div class="ml-auto max-w-xs space-y-2 border-t border-slate-200 pt-4">
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
                    <span class="text-base font-black text-slate-900">TOTAL ESTIMADO</span>
                    <span class="text-xl font-black text-blue-600"><?= formatMoney((float)$quote['total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <?php if ($quote['notes'] || $quote['terms']): ?>
        <div class="px-10 pb-8 grid grid-cols-2 gap-8 border-t border-slate-100 pt-6">
            <?php if ($quote['notes']): ?>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações</p>
                <p class="text-sm text-slate-600"><?= nl2br(htmlspecialchars($quote['notes'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($quote['terms']): ?>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Termos e Condições</p>
                <p class="text-sm text-slate-600"><?= nl2br(htmlspecialchars($quote['terms'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-200 px-10 py-4 text-center text-xs text-slate-400">
            Gerado por FaturaMZ Pro &bull; <?= date('d/m/Y H:i') ?> &bull; Este documento é apenas uma proposta comercial.
        </div>
    </div>
</body>
</html>
