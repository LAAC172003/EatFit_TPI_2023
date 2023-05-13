<?php
/** @var $exception \Exception */
\Eatfit\Site\Core\Application::$app->response->statusCode($exception->getCode());
$this->title = $exception->getCode();
?>
<main class="error-container">
    <div class="recipe-detail">
        <div class="recipe-title"><?php echo $exception->getMessage() ?></div>
    </div>
    <div class="consume-recipe-button-container">
        <a href="/" class="consume-recipe-button">Retour à la page d'accueil</a>
    </div>
</main>
