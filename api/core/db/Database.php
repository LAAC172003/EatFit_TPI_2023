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
     * Établit une connexion à la base de données en utilisant la configuration fournie.
     *
     * @param array $config La configuration de connexion à la base de données.
     * @throws PDOException Si une erreur survient lors de la connexion à la base de données.
     */
    private static function connect(array $config): void
    {
        try {
            self::$pdo = new PDO($config['dsn'], $config['user'], $config['password']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException("La connexion à la base de données a échoué", 400);
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

    /**
     * Récupère le dernier identifiant inséré dans la base de données.
     *
     * @return false|string L'identifiant inséré ou false si aucun identifiant n'est disponible.
     */
    public static function getLastInsertId(): false|string
    {
        return self::$pdo->lastInsertId();
    }

    /**
     * Démarre une transaction.
     */
    public function beginTransaction(): void
    {
        self::$pdo->beginTransaction();
    }


    /**
     * Valide une transaction.
     */
    public function commit(): void
    {
        self::$pdo->commit();
    }

    /**
     * Annule une transaction.
     */
    public function rollBack(): void
    {
        self::$pdo->rollBack();
    }
}
