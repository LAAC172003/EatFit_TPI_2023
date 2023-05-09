<?php


use Eatfit\Site\Controllers\AboutController;
use Eatfit\Site\Controllers\SiteController;
use Eatfit\Site\Core\Application;
use Eatfit\Site\Models\User;

require_once __DIR__ . '/../vendor/autoload.php';

$config = [
    'API_URL' => "http://eatfittpi2023/"
];

$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/detail', [SiteController::class, 'detail']);


$app->router->get('/login', [SiteController::class, 'login']);
$app->router->post('/login', [SiteController::class, 'login']);

$app->router->get('/profile', [SiteController::class, 'profile']);
$app->router->post('/profile/{method}/', [SiteController::class, 'profile']);

$app->router->get('/recipe', [SiteController::class, 'recipe']);
$app->router->post('/recipe', [SiteController::class, 'recipe']);


$app->router->get('/register', [SiteController::class, 'register']);
$app->router->post('/register', [SiteController::class, 'register']);
$app->router->get('/login/{id}', [SiteController::class, 'login']);
$app->router->get('/logout', [SiteController::class, 'logout']);
$app->router->get('/contact', [SiteController::class, 'contact']);
$app->router->get('/about', [AboutController::class, 'index']);
$app->router->get('/profile/{id:\d+}/{username}', [SiteController::class, 'login']);
// /profile/{id}
// /profile/13
// \/profile\/\w+

// /profile/{id}/zura
// /profile/12/zura

// /{id}
$app->run();
