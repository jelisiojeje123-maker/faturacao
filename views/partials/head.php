<?php
/**
 * Layout partilhado - Head HTML
 * $pageTitle e $extraHead podem ser definidos na view
 */
$pageTitle = $pageTitle ?? 'FaturaMZ Pro';
$base = '/faturacao';
?>
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($pageTitle) ?> — FaturaMZ Pro</title>
    <meta name="description" content="Sistema de Faturação para Prestadores de Serviços em Moçambique">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    "primary":           "#2563eb",
                    "primary-dark":      "#1d4ed8",
                    "surface":           "#f8fafc",
                    "surface-card":      "#ffffff",
                    "on-surface":        "#0f172a",
                    "on-surface-muted":  "#64748b",
                    "outline":           "#e2e8f0",
                    "sidebar":           "#1e293b",
                },
                fontFamily: {
                    sans: ["Inter", "ui-sans-serif", "system-ui"],
                }
            }
        }
    }
    </script>

    <!-- Inter font + Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

    <!-- Axios (AJAX) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Chart.js (para o dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            line-height: 1;
        }
        /* Scrollbar suave */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        /* Toast animations */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0);   opacity: 1; }
        }
        .toast { animation: slideIn .3s ease-out; }
        /* Modal backdrop */
        .modal-backdrop { backdrop-filter: blur(4px); }
        /* Table hover */
        tbody tr:hover td { background-color: #f8fafc; }
    </style>
    <?= $extraHead ?? '' ?>
</head>
<body class="bg-surface text-on-surface antialiased overflow-x-hidden">
