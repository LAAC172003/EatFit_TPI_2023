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
    public function profile(Request $request)
    {
        $profileModel = new ProfileModel();
        if (isset($request->getRouteParams()['method'])) {
            if ($request->getRouteParams()['method'] == 'update') {
                $profileModel->loadData($request->getBody());
                try {
                    $apiResponse = $profileModel->update();
                    if ($apiResponse->message == "OK" && $apiResponse->code == 200) {
                        Application::$app->session->setFlash('success', 'Le profil a bien été mis à jour');
                        Application::$app->session->set("user", $apiResponse->value->token);
                    } else {
                        Application::$app->session->setFlash('error', $apiResponse->message);
                    }
                    Application::$app->response->statusCode($apiResponse->code);
                    Application::$app->response->redirect('/profile');

                } catch
                (Exception $e) {
                    Application::$app->session->setFlash('error', $e->getMessage());
                }
//            }
            }
            if ($request->getRouteParams()['method'] == "delete") {
                Application::$app->logout();
                Application::$app->session->setFlash('success', 'Votre compte a bien été supprimé');
                Application::$app->response->redirect('/');
            }
        }

        // Si la requête est de type GET ou si la validation a échoué, afficher le formulaire.
        return $this->render('profile', ['model' => $profileModel, 'user' => Application::$app->user]);
    }

    public function login(Request $request)
    {
        if (!Application::isGuest()) Application::$app->response->redirect('/');
        $loginForm = new LoginForm();

        if ($request->getMethod() === 'post') {
            $loginForm->loadData($request->getBody());

            if ($loginForm->validate() && $loginForm->login()) {
                Application::$app->response->redirect('/');
            }
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
            $result = $registerModel->save();
            if ($registerModel->validate() && $result) {
                Application::$app->session->setFlash('success', $result->message);
                Application::$app->response->redirect('/');
                Application::$app->login($result);
            }
        }
        return $this->render('register', [
            'model' => $registerModel
        ]);
    }

    public function logout()
    {
        Application::$app->logout();
        Application::$app->response->redirect('/');
    }
}