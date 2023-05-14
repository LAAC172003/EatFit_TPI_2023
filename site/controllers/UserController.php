<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Models\LoginForm;
use Eatfit\Site\models\ProfileModel;
use Eatfit\Site\Models\User;
use Exception;

class UserController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    /**
     * @throws Exception
     */
    public function profile(Request $request): string
    {
        $profileModel = new ProfileModel();
        if (isset($request->getRouteParams()['method'])) {
            if ($request->getRouteParams()['method'] == 'update') {

                $profileModel->loadData($request->getBody());
                $apiResponse = $profileModel->update();
                if ($apiResponse->message == "OK" && $apiResponse->code == 200) {
                    Application::$app->session->setFlash('success', 'Le profil a bien été mis à jour');
                    Application::$app->session->set("user", $apiResponse->value->token);
                } else Application::$app->session->setFlash('error', $apiResponse->message);
                Application::$app->response->statusCode($apiResponse->code);
                Application::$app->response->redirect('/profile');
            }
            if ($request->getRouteParams()['method'] == "delete") {
                $apiResponse = $profileModel->delete();
                Application::$app->session->setFlash('success', $apiResponse->value);
                Application::$app->response->statusCode($apiResponse->code);
                Application::$app->response->redirect('/');
                Application::$app->logout();
            }
        }
        return $this->render('profile', ['model' => $profileModel, 'user' => Application::$app->user]);
    }

    public function login(Request $request): string
    {
        if (!Application::isGuest()) Application::$app->response->redirect('/');
        $loginForm = new LoginForm();
        if ($request->getMethod() === 'post') {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                Application::$app->session->setFlash('success', 'Vous êtes maintenant connecté');
            }
            Application::$app->response->redirect('/');
        }

        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    public function register(Request $request): string
    {
        if (!Application::isGuest()) Application::$app->response->redirect('/');
        $registerModel = new User();
        if ($request->getMethod() === 'post') {
            $registerModel->loadData($request->getBody());
            $apiResponse = $registerModel->save();
            if ($registerModel->validate()) {
                if (!$apiResponse) {
                    Application::$app->session->setFlash('error', 'Une erreur est survenue');
                    Application::$app->response->redirect('/register');
                }
                if ($apiResponse->code == 201 && $apiResponse->value != null) {
                    Application::$app->session->setFlash('success', $apiResponse->message);
                    Application::$app->login($apiResponse);
                    Application::$app->response->redirect('/');
                } else {
                    Application::$app->session->setFlash('error', $apiResponse->message);
                    Application::$app->response->statusCode($apiResponse->code);
                    Application::$app->response->redirect('/register');
                }
            }
        }
        return $this->render('register', [
            'model' => $registerModel
        ]);
    }

    public function logout(): void
    {
        Application::$app->logout();
        Application::$app->response->redirect('/');
    }
}