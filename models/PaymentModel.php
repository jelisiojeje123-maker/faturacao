<?php
/**
 * Model: Payment
 */

require_once __DIR__ . '/BaseModel.php';

class PaymentModel extends BaseModel
{
    protected string $table = 'payments';

    /** Listar pagamentos com JOIN */
    public function getList(int $page = 1, int $perPage = 15, string $search = '', string $method = ''): array
    {
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(i.`invoice_number` LIKE ? OR c.`name` LIKE ? OR p.`reference` LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like, $like]);
        }
        if ($method) {
            $where[] = "p.`method` = ?";
            $params[] = $method;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->query(
            "SELECT COUNT(*) FROM `payments` p
             JOIN `invoices` i ON i.`id` = p.`invoice_id`
             JOIN `clients` c ON c.`id` = i.`client_id`
             {$whereClause}", $params
        )->fetchColumn();

        $items = $this->db->query(
            "SELECT p.*, i.`invoice_number`, i.`total` AS invoice_total,
                    c.`name` AS client_name, u.`name` AS received_by_name
             FROM `payments` p
             JOIN `invoices` i ON i.`id` = p.`invoice_id`
             JOIN `clients` c ON c.`id` = i.`client_id`
             JOIN `users` u ON u.`id` = p.`created_by`
             {$whereClause}
             ORDER BY p.`payment_date` DESC
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

    /** Registar pagamento */
    public function register(array $data): int
    {
        // Gerar número de recibo
        $receiptNumber = 'REC-' . date('Y') . '-' . str_pad(
            $this->count() + 1, 4, '0', STR_PAD_LEFT
        );

        $this->db->query(
            "INSERT INTO `payments` (`invoice_id`, `amount`, `payment_date`, `method`, `reference`, `notes`, `receipt_number`, `created_by`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                (int) $data['invoice_id'],
                (float) $data['amount'],
                $data['payment_date'],
                $data['method'],
                trim($data['reference'] ?? ''),
                trim($data['notes'] ?? ''),
                $receiptNumber,
                (int) $data['created_by'],
            ]
        );
        return (int) $this->lastInsertId();
    }

    /** Total recebido por fatura */
    public function getTotalForInvoice(int $invoiceId): float
    {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(`amount`), 0) FROM `payments` WHERE `invoice_id` = ?",
            [$invoiceId]
        )->fetchColumn();
    }

    /** Totais por método de pagamento */
    public function getTotalsByMethod(string $dateFrom = '', string $dateTo = ''): array
    {
        $where = '';
        $params = [];
        if ($dateFrom && $dateTo) {
            $where = "WHERE `payment_date` BETWEEN ? AND ?";
            $params = [$dateFrom, $dateTo];
        }
        return $this->db->query(
            "SELECT `method`, COUNT(*) AS count, COALESCE(SUM(`amount`), 0) AS total
             FROM `payments` {$where}
             GROUP BY `method`",
            $params
        )->fetchAll();
    }
}
