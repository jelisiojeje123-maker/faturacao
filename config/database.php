<?php
/**
 * Classe Database - Singleton PDO
 * Sistema de Faturação - Moçambique
 */

require_once __DIR__ . '/app.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Em produção, nunca mostrar detalhes do erro
            error_log('Erro de conexão DB: ' . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Erro de conexão com a base de dados. Verifique as configurações.'
            ]));
        }
    }

    /** Retorna a instância singleton */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Retorna o objecto PDO */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /** Atalho: preparar e executar query com bind de parâmetros */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Último ID inserido */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /** Iniciar transacção */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /** Confirmar transacção */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /** Reverter transacção */
    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    // Evitar clone e deserialização
    private function __clone() {}
    public function __wakeup() { throw new \Exception('Cannot unserialize singleton.'); }
}
