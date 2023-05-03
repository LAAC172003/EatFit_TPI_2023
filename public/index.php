<?php

use eatFitTpi2023\core\ApiValue;
use eatFitTpi2023\core\Application;
use eatFitTpi2023\core\EnvLoader;
use eatFitTpi2023\core\Model;
use eatFitTpi2023\core\Request;

use eatFitTpi2023\controllers\UserController;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new EnvLoader(dirname(__DIR__) . '/.env');
try {
    $dotenv->load();
} catch (Exception $e) {
    echo new ApiValue(null, $e->getCode(), $e->getMessage());
    return;
}
$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', function (): ApiValue {
    return new ApiValue(Application::$app->router->getRoutes(), 200);
});
$app->router->get('/test', function (): ApiValue {
    return new ApiValue("test", 200);
});
$app->router->get('/user', [UserController::class, 'read']);// get User
$app->router->post('/user', [UserController::class, 'create']);// get User
$app->router->put('/user', [UserController::class, 'update']);// get User
$app->router->delete('/user', [UserController::class, 'delete']);// get User
$app->run();
