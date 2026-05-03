<?php
/**
 * Model Base - Classe abstracta base para todos os modelos
 */

require_once __DIR__ . '/../config/database.php';

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Buscar todos os registos */
    public function findAll(string $orderBy = 'created_at DESC', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY {$orderBy}";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    /** Buscar por ID */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        $result = $this->db->query($sql, [$id])->fetch();
        return $result ?: null;
    }

    /** Contar registos */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        if ($where) $sql .= " WHERE {$where}";
        return (int) $this->db->query($sql, $params)->fetchColumn();
    }

    /** Eliminar por ID */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }

    /** Último ID inserido */
    public function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
