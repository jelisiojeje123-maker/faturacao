<?php
/**
 * Model: Client
 */

require_once __DIR__ . '/BaseModel.php';

class ClientModel extends BaseModel
{
    protected string $table = 'clients';

    /** Listar clientes com paginação e filtros */
    public function getList(int $page = 1, int $perPage = 15, string $search = '', string $status = ''): array
    {
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(`name` LIKE ? OR `email` LIKE ? OR `nuit` LIKE ? OR `phone` LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        if ($status) {
            $where[] = "`status` = ?";
            $params[] = $status;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->query(
            "SELECT COUNT(*) FROM `clients` {$whereClause}", $params
        )->fetchColumn();

        $items = $this->db->query(
            "SELECT * FROM `clients` {$whereClause} ORDER BY `name` ASC LIMIT {$perPage} OFFSET {$offset}",
            $params
        )->fetchAll();

        return [
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /** Todos os clientes activos (para select) */
    public function getActive(): array
    {
        return $this->db->query(
            "SELECT `id`, `name`, `nuit`, `email` FROM `clients` WHERE `status` = 'ativo' ORDER BY `name` ASC"
        )->fetchAll();
    }

    /** Criar cliente */
    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO `clients` (`name`, `nuit`, `email`, `phone`, `address`, `city`, `country`, `status`, `notes`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                trim($data['name']),
                trim($data['nuit'] ?? ''),
                strtolower(trim($data['email'] ?? '')),
                trim($data['phone'] ?? ''),
                trim($data['address'] ?? ''),
                trim($data['city'] ?? ''),
                trim($data['country'] ?? 'Moçambique'),
                $data['status'] ?? 'ativo',
                trim($data['notes'] ?? ''),
            ]
        );
        return (int) $this->lastInsertId();
    }

    /** Actualizar cliente */
    public function update(int $id, array $data): bool
    {
        return $this->db->query(
            "UPDATE `clients` SET `name`=?, `nuit`=?, `email`=?, `phone`=?, `address`=?, `city`=?, `country`=?, `status`=?, `notes`=?
             WHERE `id` = ?",
            [
                trim($data['name']),
                trim($data['nuit'] ?? ''),
                strtolower(trim($data['email'] ?? '')),
                trim($data['phone'] ?? ''),
                trim($data['address'] ?? ''),
                trim($data['city'] ?? ''),
                trim($data['country'] ?? 'Moçambique'),
                $data['status'] ?? 'ativo',
                trim($data['notes'] ?? ''),
                $id,
            ]
        )->rowCount() > 0;
    }

    /** Estatísticas do cliente (faturas, total pago) */
    public function getStats(int $clientId): array
    {
        $stats = $this->db->query(
            "SELECT
               COUNT(*)                                          AS total_invoices,
               SUM(CASE WHEN status='paga' THEN 1 ELSE 0 END)  AS paid_invoices,
               SUM(CASE WHEN status='vencida' THEN 1 ELSE 0 END) AS overdue_invoices,
               COALESCE(SUM(total), 0)                          AS total_billed,
               COALESCE(SUM(amount_paid), 0)                    AS total_paid,
               COALESCE(SUM(amount_due), 0)                     AS total_due
             FROM `invoices` WHERE `client_id` = ?",
            [$clientId]
        )->fetch();
        return $stats ?: [];
    }
}
