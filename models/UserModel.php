<?php
/**
 * Model: User
 */

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected string $table = 'users';

    /** Buscar utilizador por email */
    public function findByEmail(string $email): ?array
    {
        $result = $this->db->query(
            "SELECT * FROM `users` WHERE `email` = ? AND `is_active` = 1 LIMIT 1",
            [strtolower(trim($email))]
        )->fetch();
        return $result ?: null;
    }

    /** Autenticar utilizador */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) return null;
        if (!password_verify($password, $user['password'])) return null;
        return $user;
    }

    /** Criar utilizador */
    public function create(array $data): int
    {
        $this->db->query(
            "INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (?, ?, ?, ?)",
            [
                trim($data['name']),
                strtolower(trim($data['email'])),
                password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $data['role'] ?? 'operador',
            ]
        );
        return (int) $this->lastInsertId();
    }

    /** Actualizar perfil */
    public function update(int $id, array $data): bool
    {
        $sets = [];
        $params = [];
        $allowed = ['name', 'email', 'role', 'is_active', 'avatar'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`{$field}` = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($sets)) return false;
        $params[] = $id;
        return $this->db->query(
            "UPDATE `users` SET " . implode(', ', $sets) . " WHERE `id` = ?",
            $params
        )->rowCount() > 0;
    }

    /** Alterar senha */
    public function changePassword(int $id, string $newPassword): bool
    {
        return $this->db->query(
            "UPDATE `users` SET `password` = ? WHERE `id` = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]
        )->rowCount() > 0;
    }
}
