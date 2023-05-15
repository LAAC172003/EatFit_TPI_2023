<?php

namespace Eatfit\Site\Core;

use Eatfit\Site\Core\Db\Database;
use Eatfit\Site\Core\Db\DbModel;
use Eatfit\Site\Models\User;
use Exception;

class Application
{

    public static Application $app;
    public static string $ROOT_DIR;
    public static array $ALLOWED_IMAGE_EXTENSIONS;
    public static string $API_URL;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Session $session;
    public View $view;
    public $user;

    /**
     * Constructeur de l'application.
     *
     * @param string $rootDir Le répertoire racine de l'application.
     * @param array $config La configuration de l'application.
     */
    public function __construct(string $rootDir, array $config)
    {
        $this->user = null;
        self::$API_URL = $config['API_URL'];
        self::$ALLOWED_IMAGE_EXTENSIONS = $config['ALLOWED_IMAGE_EXTENSIONS'];
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->view = new View();
        $token = Application::$app->session->get('user');

        if ($token != null) {
            $user = User::getUserByToken($token);
            if (isset($user->value->token) == null && isset($user->value->expiration) == null) {
                $this->session->setFlash("error", "Votre session a expiré, veuillez vous reconnecter");
                $this->logout();
            } else $this->user = $user->value;
        }
    }

    /**
     * Déconnecte l'utilisateur en supprimant son token de la session.
     */
    public function logout(): void
    {
        $this->user = null;
        self::$app->session->remove('user');
    }

    /**
     * Vérifie si l'utilisateur est un invité (non authentifié).
     *
     * @return bool True si l'utilisateur est un invité, False sinon.
     */
    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    /**
     * Connecte l'utilisateur en enregistrant son token dans la session et effectue une redirection.
     *
     * @param mixed $user Les informations de l'utilisateur.
     * @return bool True si la connexion réussit, False sinon.
     */
    public function login(mixed $user): bool
    {
        Application::$app->session->set('user', $user->value->token);
        $this->response->statusCode($user->code);
        Application::$app->response->redirect('/');
        return true;
    }

    /**
     * Lance l'application.
     */
    public function run(): void
    {
        try {
            echo $this->router->resolve();
        } catch (Exception $e) {
            echo $this->router->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }
}