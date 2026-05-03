<?php
/**
 * Model: Invoice
 */

require_once __DIR__ . '/BaseModel.php';

class InvoiceModel extends BaseModel
{
    protected string $table = 'invoices';

    /** Listar faturas com JOIN ao cliente */
    public function getList(int $page = 1, int $perPage = 15, string $search = '', string $status = '', int $clientId = 0): array
    {
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(i.`invoice_number` LIKE ? OR c.`name` LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like]);
        }
        if ($status) {
            $where[] = "i.`status` = ?";
            $params[] = $status;
        }
        if ($clientId) {
            $where[] = "i.`client_id` = ?";
            $params[] = $clientId;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->query(
            "SELECT COUNT(*) FROM `invoices` i JOIN `clients` c ON c.id = i.client_id {$whereClause}", $params
        )->fetchColumn();

        $items = $this->db->query(
            "SELECT i.*, c.`name` AS client_name, c.`email` AS client_email, c.`nuit` AS client_nuit
             FROM `invoices` i
             JOIN `clients` c ON c.`id` = i.`client_id`
             {$whereClause}
             ORDER BY i.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        )->fetchAll();

        return [
            'items'     => $items,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /** Buscar fatura com detalhes completos */
    public function findWithDetails(int $id): ?array
    {
        $invoice = $this->db->query(
            "SELECT i.*, c.`name` AS client_name, c.`email` AS client_email,
                    c.`nuit` AS client_nuit, c.`phone` AS client_phone,
                    c.`address` AS client_address, c.`city` AS client_city,
                    u.`name` AS created_by_name
             FROM `invoices` i
             JOIN `clients` c ON c.`id` = i.`client_id`
             JOIN `users` u ON u.`id` = i.`created_by`
             WHERE i.`id` = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$invoice) return null;

        $invoice['items'] = $this->db->query(
            "SELECT ii.*, s.`name` AS service_name
             FROM `invoice_items` ii
             LEFT JOIN `services` s ON s.`id` = ii.`service_id`
             WHERE ii.`invoice_id` = ?
             ORDER BY ii.`sort_order` ASC",
            [$id]
        )->fetchAll();

        $invoice['payments'] = $this->db->query(
            "SELECT p.*, u.`name` AS received_by
             FROM `payments` p
             JOIN `users` u ON u.`id` = p.`created_by`
             WHERE p.`invoice_id` = ?
             ORDER BY p.`payment_date` ASC",
            [$id]
        )->fetchAll();

        return $invoice;
    }

    /** Criar fatura com itens (transacção) */
    public function create(array $data, array $items): int
    {
        $this->db->beginTransaction();
        try {
            // Calcular totais
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['quantity'] * (float)$item['unit_price'];
            }
            $ivaRate   = (float) ($data['iva_rate'] ?? DEFAULT_IVA_RATE);
            $ivaAmount = round($subtotal * ($ivaRate / 100), 2);
            $discount  = (float) ($data['discount'] ?? 0);
            
            $retRate   = (float) ($data['retencao_rate'] ?? 0);
            $retAmount = round($subtotal * ($retRate / 100), 2);
            
            $total     = round($subtotal + $ivaAmount - $discount - $retAmount, 2);

            // Inserir fatura
            $this->db->query(
                "INSERT INTO `invoices`
                 (`client_id`, `quote_id`, `invoice_number`, `issue_date`, `due_date`, `status`,
                  `subtotal`, `iva_rate`, `iva_amount`, `retencao_rate`, `retencao_amount`,
                  `discount`, `total`, `amount_paid`, `amount_due`, `notes`, `terms`, `created_by`)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,?,?)",
                [
                    (int) $data['client_id'],
                    !empty($data['quote_id']) ? (int) $data['quote_id'] : null,
                    $data['invoice_number'],
                    $data['issue_date'],
                    $data['due_date'],
                    $data['status'] ?? 'rascunho',
                    $subtotal,
                    $ivaRate,
                    $ivaAmount,
                    $retRate,
                    $retAmount,
                    $discount,
                    $total,
                    $total,  // amount_due = total inicialmente
                    trim($data['notes'] ?? ''),
                    trim($data['terms'] ?? ''),
                    (int) $data['created_by'],
                ]
            );
            $invoiceId = (int) $this->lastInsertId();

            // Inserir itens
            foreach ($items as $i => $item) {
                $lineTotal = round((float)$item['quantity'] * (float)$item['unit_price'], 2);
                $this->db->query(
                    "INSERT INTO `invoice_items` (`invoice_id`, `service_id`, `description`, `quantity`, `unit_price`, `total`, `sort_order`)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $invoiceId,
                        !empty($item['service_id']) ? (int)$item['service_id'] : null,
                        trim($item['description']),
                        (float) $item['quantity'],
                        (float) $item['unit_price'],
                        $lineTotal,
                        $i + 1,
                    ]
                );
            }

            // Incrementar contador de faturas
            $this->db->query("UPDATE `company_settings` SET `next_invoice_number` = `next_invoice_number` + 1 WHERE `id` = 1");

            $this->db->commit();
            return $invoiceId;

        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /** Actualizar estado da fatura */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->query(
            "UPDATE `invoices` SET `status` = ? WHERE `id` = ?",
            [$status, $id]
        )->rowCount() > 0;
    }

    /** Atualizar valores pagos após pagamento */
    public function recalculateAmounts(int $invoiceId): void
    {
        $totalPaid = (float) $this->db->query(
            "SELECT COALESCE(SUM(`amount`), 0) FROM `payments` WHERE `invoice_id` = ?",
            [$invoiceId]
        )->fetchColumn();

        $invoice = $this->findById($invoiceId);
        if (!$invoice) return;

        $amountDue = max(0, (float)$invoice['total'] - $totalPaid);
        $status    = $amountDue <= 0 ? 'paga' : $invoice['status'];

        $this->db->query(
            "UPDATE `invoices` SET `amount_paid` = ?, `amount_due` = ?, `status` = ? WHERE `id` = ?",
            [$totalPaid, $amountDue, $status, $invoiceId]
        );
    }

    /** Marcar faturas vencidas automaticamente */
    public function markOverdue(): int
    {
        $stmt = $this->db->query(
            "UPDATE `invoices` SET `status` = 'vencida'
             WHERE `status` = 'emitida' AND `due_date` < CURDATE() AND `amount_due` > 0"
        );
        return $stmt->rowCount();
    }

    /** KPIs para o dashboard */
    public function getDashboardKpis(): array
    {
        return $this->db->query(
            "SELECT
               COALESCE(SUM(total), 0)                                      AS total_billed,
               COALESCE(SUM(amount_paid), 0)                                AS total_received,
               COALESCE(SUM(CASE WHEN status='emitida' THEN amount_due ELSE 0 END), 0) AS pending,
               COALESCE(SUM(CASE WHEN status='vencida' THEN amount_due ELSE 0 END), 0) AS overdue,
               COUNT(CASE WHEN status='emitida' THEN 1 END)                 AS count_pending,
               COUNT(CASE WHEN status='vencida' THEN 1 END)                 AS count_overdue,
               COUNT(*)                                                       AS total_invoices
             FROM `invoices`"
        )->fetch();
    }

    /** Dados do gráfico mensal (últimos 12 meses) */
    public function getMonthlyChart(): array
    {
        return $this->db->query(
            "SELECT
               DATE_FORMAT(`issue_date`, '%Y-%m') AS month_key,
               DATE_FORMAT(`issue_date`, '%b/%Y')  AS month_label,
               COALESCE(SUM(`total`), 0)            AS billed,
               COALESCE(SUM(`amount_paid`), 0)      AS received
             FROM `invoices`
             WHERE `issue_date` >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month_key, month_label
             ORDER BY month_key ASC"
        )->fetchAll();
    }

    /** Faturas próximas do vencimento */
    public function getDueSoon(int $days = 7): array
    {
        return $this->db->query(
            "SELECT i.*, c.`name` AS client_name
             FROM `invoices` i
             JOIN `clients` c ON c.`id` = i.`client_id`
             WHERE i.`status` IN ('emitida','vencida')
               AND i.`due_date` <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY i.`due_date` ASC
             LIMIT 5",
            [$days]
        )->fetchAll();
    }

    /** Relatório por período */
    public function getReport(string $dateFrom, string $dateTo): array
    {
        return $this->db->query(
            "SELECT i.*, c.`name` AS client_name
             FROM `invoices` i
             JOIN `clients` c ON c.`id` = i.`client_id`
             WHERE i.`issue_date` BETWEEN ? AND ?
             ORDER BY i.`issue_date` ASC",
            [$dateFrom, $dateTo]
        )->fetchAll();
    }

    /** Resumo financeiro por período */
    public function getReportSummary(string $dateFrom, string $dateTo): array
    {
        return $this->db->query(
            "SELECT
               COUNT(*)                                     AS total_invoices,
               COALESCE(SUM(total), 0)                     AS total_billed,
               COALESCE(SUM(amount_paid), 0)               AS total_received,
               COALESCE(SUM(amount_due), 0)                AS total_due,
               COALESCE(SUM(iva_amount), 0)                AS total_iva,
               COUNT(CASE WHEN status='paga' THEN 1 END)   AS count_paid,
               COUNT(CASE WHEN status='emitida' THEN 1 END) AS count_pending,
               COUNT(CASE WHEN status='vencida' THEN 1 END) AS count_overdue
             FROM `invoices`
             WHERE `issue_date` BETWEEN ? AND ?",
            [$dateFrom, $dateTo]
        )->fetch();
    }
}
