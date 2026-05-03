<?php
/**
 * View: Lista de Orçamentos
 */
$pageTitle   = 'Orçamentos';
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

        <div class="p-6 lg:p-8 max-w-[1600px] w-full mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Orçamentos</h1>
                    <p class="text-slate-500 text-sm mt-1"><?= number_format($total) ?> orçamentos e propostas</p>
                </div>
                <a href="/faturacao/criar-orcamento.php"
                   class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all active:scale-95 text-sm shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Novo Orçamento
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Pesquisar por número ou cliente..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                    </div>
                    <select name="status" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Todos os estados</option>
                        <option value="rascunho"   <?= ($status ?? '') === 'rascunho'   ? 'selected' : '' ?>>Rascunho</option>
                        <option value="enviado"    <?= ($status ?? '') === 'enviado'    ? 'selected' : '' ?>>Enviado</option>
                        <option value="aceite"     <?= ($status ?? '') === 'aceite'     ? 'selected' : '' ?>>Aceite</option>
                        <option value="convertido" <?= ($status ?? '') === 'convertido' ? 'selected' : '' ?>>Convertido</option>
                        <option value="recusado"   <?= ($status ?? '') === 'recusado'   ? 'selected' : '' ?>>Recusado</option>
                        <option value="expirado"   <?= ($status ?? '') === 'expirado'   ? 'selected' : '' ?>>Expirado</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all">
                        Filtrar
                    </button>
                    <?php if (!empty($search) || !empty($status)): ?>
                    <a href="/faturacao/orcamentos.php" class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm font-medium flex items-center">
                        Limpar
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-3 text-left">Nº Orçamento</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Data de Emissão</th>
                            <th class="px-6 py-3 text-left">Validade</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $quote): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="/faturacao/orcamentos.php?id=<?= $quote['id'] ?>"
                                   class="font-mono text-blue-600 hover:underline text-xs font-semibold">
                                    <?= htmlspecialchars($quote['quote_number']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                                        <?= getInitials($quote['client_name']) ?>
                                    </div>
                                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($quote['client_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?= formatDate($quote['issue_date']) ?></td>
                            <td class="px-6 py-4 text-slate-500 <?= ($quote['expiry_date'] && strtotime($quote['expiry_date']) < time() && $quote['status'] === 'enviado') ? 'text-rose-500 font-semibold' : '' ?>">
                                <?= $quote['expiry_date'] ? formatDate($quote['expiry_date']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900"><?= formatMoney((float)$quote['total']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $statusClass = match($quote['status']) {
                                    'rascunho'   => 'bg-slate-100 text-slate-600',
                                    'enviado'    => 'bg-blue-100 text-blue-700',
                                    'aceite'     => 'bg-emerald-100 text-emerald-700',
                                    'recusado'   => 'bg-rose-100 text-rose-700',
                                    'expirado'   => 'bg-orange-100 text-orange-700',
                                    'convertido' => 'bg-purple-100 text-purple-700',
                                    default      => 'bg-gray-100 text-gray-700',
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= $statusClass ?>">
                                    <?= htmlspecialchars($quote['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="/faturacao/orcamentos.php?id=<?= $quote['id'] ?>"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver detalhes">
                                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                    </a>
                                    <a href="/faturacao/orcamentos.php?action=print&id=<?= $quote['id'] ?>" target="_blank"
                                       class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all" title="Imprimir/PDF">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                        <tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">request_quote</span>
                            Nenhum orçamento encontrado.
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($last_page > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-sm">
                    <p class="text-slate-500">Página <?= $page ?> de <?= $last_page ?></p>
                    <div class="flex gap-1">
                        <?php for ($p = 1; $p <= $last_page; $p++): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status ?? '') ?>"
                           class="px-3 py-1.5 rounded-lg font-semibold <?= $p === $page ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?> transition-all">
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
</body>
</html>
