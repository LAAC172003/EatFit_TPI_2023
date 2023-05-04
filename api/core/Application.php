<?php

namespace Eatfit\Api\Core;

use Eatfit\Api\Core\Db\Database;
use Exception;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Database $db;
    private array $config;

    /**
     * Constructeur de l'application.
     *
     * @param string $rootDir Le répertoire racine de l'application
     * @param array $config La configuration de l'application
     */
    public function __construct(string $rootDir, array $config)
    {
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->config = $config;
    }

    /**
     * Se connecte à la base de données.
     */
    private function connectToDatabase(): void
    {
        if (!isset($this->db)) $this->db = new Database($this->config['db']);
    }

    /**
     * Lance l'application
     * Résout la requête et renvoie la réponse correspondante
     * Si une exception est levée, renvoie une réponse d'erreur
     */
    public function run(): void
    {
        try {
            $this->connectToDatabase();
            echo $this->router->resolve();
        } catch (Exception $e) {
            echo new ApiValue(null, $e->getCode(), $e->getMessage());
        }
    }
}