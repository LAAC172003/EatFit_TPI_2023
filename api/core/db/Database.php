<?php

namespace Eatfit\Api\Core\Db;

use Exception;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public function __construct(array $config)
    {
        if (self::$pdo === null) self::connect($config);
    }

    /**
     * Connects to the database using the provided configuration.
     *
     * @param array $config The database connection configuration.
     * @throws PDOException If an error occurs while connecting to the database.
     */
    private static function connect(array $config): void
    {
        try {
            self::$pdo = new PDO($config['dsn'], $config['user'], $config['password']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage(), 400);
        }
    }

    /**
     * Exécute une requête SQL préparée avec des paramètres.
     *
     * @param string $query La requête SQL préparée.
     * @param array $params Un tableau associatif contenant les valeurs des paramètres.
     * @return SqlResult|false Un objet SqlResult contenant le résultat de la requête ou false si une erreur survient.
     * @throws PDOException Si la requête est vide ou si une erreur survient lors de l'exécution de la requête.
     * @throws Exception
     */
    // $query = execute("SELECT * FROM users WHERE id = :id AND name = :name", [":id"=>1, ":name" => "John"]));
    public static function execute(string $query, array $params = []): SqlResult|false
    {
        if (empty($query)) throw new Exception("Query cannot be empty.", 400);
        if (self::$pdo === null) throw new Exception("Database connection not initialized.", 500);
        $stmt = self::$pdo->prepare($query);
        $stmt->execute($params);
        return new SqlResult($stmt);
    }

    public static function getLastInsertId(): false|string
    {
        return self::$pdo->lastInsertId();
    }

    public function beginTransaction(): void
    {
        self::$pdo->beginTransaction();
    }

    public function commit(): void
    {
        self::$pdo->commit();
    }

    public function rollBack(): void
    {
        self::$pdo->rollBack();
    }
}
