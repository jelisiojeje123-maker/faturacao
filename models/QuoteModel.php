<?php
/**
 * Model: Orçamentos
 */

require_once __DIR__ . '/BaseModel.php';

class QuoteModel extends BaseModel
{
    protected string $table = 'quotes';

    /** Listar todos os orçamentos com dados do cliente */
    public function getList(int $page = 1, int $limit = 10, string $search = '', string $status = ''): array
    {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where  = "1=1";

        if ($search) {
            $where .= " AND (q.quote_number LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($status) {
            $where .= " AND q.status = ?";
            $params[] = $status;
        }

        $total = $this->db->query("
            SELECT COUNT(*) 
            FROM quotes q 
            JOIN clients c ON q.client_id = c.id
            WHERE $where
        ", $params)->fetchColumn();

        $items = $this->db->query("
            SELECT q.*, c.name as client_name, c.email as client_email,
                   u.name as created_by_name
            FROM quotes q
            JOIN clients c ON q.client_id = c.id
            JOIN users u ON q.created_by = u.id
            WHERE $where
            ORDER BY q.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params)->fetchAll();

        return [
            'items'     => $items,
            'total'     => (int)$total,
            'page'      => $page,
            'last_page' => ceil($total / $limit)
        ];
    }

    /** Buscar orçamento com detalhes e itens */
    public function getWithDetails(int $id): ?array
    {
        $quote = $this->db->query("
            SELECT q.*, c.name as client_name, c.nuit as client_nuit, 
                   c.email as client_email, c.address as client_address,
                   u.name as created_by_name
            FROM quotes q
            JOIN clients c ON q.client_id = c.id
            JOIN users u ON q.created_by = u.id
            WHERE q.id = ?
        ", [$id])->fetch();

        if ($quote) {
            $quote['items'] = $this->db->query("
                SELECT * FROM quote_items 
                WHERE quote_id = ? 
                ORDER BY sort_order ASC
            ", [$id])->fetchAll();
        }

        return $quote ?: null;
    }

    /** Criar novo orçamento */
    public function create(array $data, array $items): int
    {
        error_log("QuoteModel::create started");
        try {
            $this->db->beginTransaction();

            // Gerar número do orçamento
            $settings = $this->db->query("SELECT invoice_prefix FROM company_settings LIMIT 1")->fetch();
            $prefix = $settings['invoice_prefix'] ?? 'ORC';
            
            $lastQuote = $this->db->query("SELECT quote_number FROM quotes ORDER BY id DESC LIMIT 1")->fetchColumn();
            $nextNum = 1;
            if ($lastQuote) {
                $parts = explode('-', $lastQuote);
                $nextNum = (int)end($parts) + 1;
            }
            $quoteNumber = sprintf("%s-ORC-%s-%04d", $prefix, date('Y'), $nextNum);

            // Inserir Orçamento
            $this->db->query("
                INSERT INTO quotes (
                    client_id, quote_number, issue_date, expiry_date, status,
                    subtotal, iva_amount, discount, total, notes, terms, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $data['client_id'],
                $quoteNumber,
                $data['issue_date'],
                $data['expiry_date'] ?? null,
                $data['status'] ?? 'enviado',
                $data['subtotal'],
                $data['iva_amount'],
                $data['discount'] ?? 0,
                $data['total'],
                $data['notes'] ?? null,
                $data['terms'] ?? null,
                $_SESSION['user_id']
            ]);

            $quoteId = (int)$this->db->lastInsertId();

            // Inserir Itens
            foreach ($items as $index => $item) {
                $this->db->query("
                    INSERT INTO quote_items (
                        quote_id, service_id, description, quantity, unit_price, total, sort_order
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ", [
                    $quoteId,
                    $item['service_id'] ?: null,
                    $item['description'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total'],
                    $index
                ]);
            }

            $this->db->commit();
            return $quoteId;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Quote Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /** Actualizar estado */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['rascunho', 'enviado', 'aceite', 'recusado', 'expirado', 'convertido'];
        if (!in_array($status, $allowed)) return false;

        return $this->db->query("UPDATE quotes SET status = ? WHERE id = ?", [$status, $id])->rowCount() > 0;
    }
}
