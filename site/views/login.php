<?php

/** @var $model LoginForm */

/** @var $this View */

use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Core\View;
use Eatfit\Site\Models\LoginForm;

$this->title = 'Login';
?>
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="login-form p-4 mt-5">
            <h2 class="text-center mb-4">Login</h2>
            <?php $form = Form::begin('', 'post') ?>
            <div class="mb-3">
                <?php echo $form->field($model, 'email')->setPlaceHolder("Entrez votre email") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="mb-3">
                <?php echo $form->field($model, 'password')->passwordField()->setPlaceHolder("Entrez votre mot de passe") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
            <br>
            <div class="mb-3">
                <a href="/register"> Vous inscrire ?</a>
            </div>
            <?php if ($model->getFirstError("error") != null) echo '   <small class="form-text text-danger help-block">' . $model->getFirstError("error") . '</small>' ?>
            <?php Form::end() ?>
        </div>
    </div>
</div>