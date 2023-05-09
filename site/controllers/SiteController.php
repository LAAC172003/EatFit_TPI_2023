<?php

namespace Eatfit\Site\Controllers;


use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Core\Response;
use Eatfit\Site\Models\LoginForm;
use Eatfit\Site\models\ProfileModel;
use Eatfit\Site\Models\User;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    public function home(): string
    {
        return $this->render('home', [
            'name' => 'Lucas Almeida Costa'
        ]);
    }

    public function detail()
    {
        return $this->render('recipe_details');
    }


    public function login(Request $request)
    {
//        echo '<pre>';
//        var_dump($request->getBody(), $request->getRouteParam('id'));
//        echo '</pre>';
        $loginForm = new LoginForm();

        if ($request->getMethod() === 'post') {
            $loginForm->loadData($request->getBody());

            if ($loginForm->validate() && $loginForm->login()) {
                Application::$app->response->redirect('/');
            }
        }
//        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    public function register(Request $request, Response $response): string
    {
        $registerModel = new User();
        if ($request->getMethod() === 'post') {
            $registerModel->loadData($request->getBody());
            $result = $registerModel->save();
            if ($registerModel->validate() && $result) {
                Application::$app->login($result);
                Application::$app->session->setFlash('success', $result->message);
            }
            $response->redirect('/');
        }
        return $this->render('register', [
            'model' => $registerModel
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        Application::$app->logout();
        $response->redirect('/');
    }

    public function contact()
    {
        return $this->render('contact');
    }

    /**
     * @throws \Exception
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
                (\Exception $e) {
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


    public
    function profileWithId(Request $request)
    {
        echo '<pre>';
        var_dump($request->getBody());
        echo '</pre>';
    }
}
