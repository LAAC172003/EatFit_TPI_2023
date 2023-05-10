<?php

namespace Eatfit\Site\Core;

use Eatfit\Site\Core\Db\Database;
use Eatfit\Site\Core\Db\DbModel;
use Eatfit\Site\Models\User;

class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

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

    public function __construct($rootDir, $config)
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

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    public function login($user)
    {
        Application::$app->session->set('user', $user->value->token);
        $this->response->statusCode($user->code);
        Application::$app->response->redirect('/');
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        self::$app->session->remove('user');
    }

    public function run(): void
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            echo $this->router->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }

    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }
}