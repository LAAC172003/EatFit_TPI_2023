<?php

namespace Eatfit\Site\Controllers;


use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Core\Response;
use Eatfit\Site\Models\LoginForm;
use Eatfit\Site\models\ProfileModel;
use Eatfit\Site\Models\Recipe;
use Eatfit\Site\Models\User;

class SiteController extends Controller
{

    function getProducts()
    {
        $dejeuner = [
            "nomPlat" => "TEst3",
            "categorie" => "dejeuner",
            "description" => "test",
            "ingredients" => [
                "pain",
                "jambon",
                "fromage",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $diner = [
            "nomPlat" => "Tets2",
            "description" => "test",
            "categorie" => "diner",
            "ingredients" => [
                "qq",
                "ww",
                "fromage",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $dessert = [
            "nomPlat" => "Test",
            "categorie" => "dessert",
            "description" => "test",
            "ingredients" => [
                "paiddn",
                "jambon",
                "asd",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $arrayProducts = [
            "déjeuner" => $dejeuner,
            "diner" => $diner,
            "dessert" => $dessert
        ];
        return $arrayProducts;
    }

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    public function home(): string
    {

        return $this->render('home', [
            'name' => 'Lucas Almeida Costa', 'products' => self::getProducts()
        ]);
    }

    public function recipe(Request $request)
    {
        if (Application::isGuest()) Application::$app->response->redirect('/login');
        $model = new Recipe();
        if (Application::$app->request->isPost()) {
            $model->loadData($request->getBody());
//            if ($model->validate() && $model->save()) {
//                Application::$app->session->setFlash('success', 'Recipe added');
//                Application::$app->response->redirect('/recipe');
//                return;
//            }
        }
        return $this->render('add_recipe', [
            'model' => $model
        ]);
    }

    public function detail()
    {
        return $this->render('recipe_details');
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
