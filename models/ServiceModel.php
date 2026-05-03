<?php
/**
 * Model: Service
 */

require_once __DIR__ . '/BaseModel.php';

class ServiceModel extends BaseModel
{
    protected string $table = 'services';

    /** Listar serviços com paginação e filtros */
    public function getList(int $page = 1, int $perPage = 15, string $search = '', string $type = ''): array
    {
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(`name` LIKE ? OR `description` LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like]);
        }
        if ($type) {
            $where[] = "`type` = ?";
            $params[] = $type;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->query(
            "SELECT COUNT(*) FROM `services` {$whereClause}", $params
        )->fetchColumn();

        $items = $this->db->query(
            "SELECT * FROM `services` {$whereClause} ORDER BY `name` ASC LIMIT {$perPage} OFFSET {$offset}",
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

    /** Todos os serviços activos (para select) */
    public function getActive(): array
    {
        return $this->db->query(
            "SELECT * FROM `services` WHERE `is_active` = 1 ORDER BY `name` ASC"
        )->fetchAll();
    }

    /** Criar serviço */
    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO `services` (`name`, `description`, `price`, `unit`, `type`, `iva_exempt`, `is_active`)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                trim($data['name']),
                trim($data['description'] ?? ''),
                (float) ($data['price'] ?? 0),
                trim($data['unit'] ?? 'un'),
                $data['type'] ?? 'pontual',
                (int) ($data['iva_exempt'] ?? 0),
                (int) ($data['is_active'] ?? 1),
            ]
        );
        return (int) $this->lastInsertId();
    }

    /** Actualizar serviço */
    public function update(int $id, array $data): bool
    {
        return $this->db->query(
            "UPDATE `services` SET `name`=?, `description`=?, `price`=?, `unit`=?, `type`=?, `iva_exempt`=?, `is_active`=?
             WHERE `id` = ?",
            [
                trim($data['name']),
                trim($data['description'] ?? ''),
                (float) ($data['price'] ?? 0),
                trim($data['unit'] ?? 'un'),
                $data['type'] ?? 'pontual',
                (int) ($data['iva_exempt'] ?? 0),
                (int) ($data['is_active'] ?? 1),
                $id,
            ]
        )->rowCount() > 0;
    }
}
