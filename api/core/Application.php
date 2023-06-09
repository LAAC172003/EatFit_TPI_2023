<?php

namespace Eatfit\Api\Core;

use Eatfit\Api\Core\Db\Database;
use Exception;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public static string $UPLOAD_PATH;
    public Router $router;
    public Request $request;
    public Database $db;
    private array $config;

    /**
     * Constructeur de l'application.
     *
     * @param string $rootDir Le répertoire racine de l'application.
     * @param array $config La configuration de l'application.
     * @throws Exception
     */
    public function __construct(string $rootDir, array $config)
    {
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->config = $config;
        self::$UPLOAD_PATH = $this->config['UPLOAD_PATH'];
    }

    /**
     * Lance l'application.
     * Résout la requête et renvoie la réponse correspondante.
     * En cas d'exception, renvoie une réponse d'erreur.
     */
    public function run(): void
    {
        try {
            if (!is_dir(self::$UPLOAD_PATH)) {
                if (!mkdir(self::$UPLOAD_PATH, 0777, true)) {
                    throw new Exception(sprintf('Échec de la création du répertoire "%s"', self::$UPLOAD_PATH));
                }
            }
            $this->connectToDatabase();
            echo $this->router->resolve();
        } catch (Exception $e) {
            echo new ApiValue(null, $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Établit la connexion à la base de données.
     *
     * @throws Exception
     */
    private function connectToDatabase(): void
    {
        if (!isset($this->db)) {
            $this->db = new Database($this->config['db']);
        }
    }
}