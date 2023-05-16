<?php
/** @var $model User */

use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\User;

$this->title = 'Register';
$form = new Form();
?>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="login-form mt-5 p-4">
            <h2 class="text-center mb-4">Inscription</h2>
            <?php $form = Form::begin('', 'post') ?>
            <div class="mb-3">
                <?php echo $form->field($model, 'username')->setPlaceHolder("Entrez votre nom d'utilisateur") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="mb-3">
                <?php echo $form->field($model, 'email')->setPlaceHolder("Entrez votre email") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="mb-3">
                <?php echo $form->field($model, 'password')->passwordField()->setPlaceHolder("Entrez votre mot de passe") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="mb-3">
                <?php echo $form->field($model, 'password_confirm')->passwordField()->setPlaceHolder("Confirmez votre mot de passe") ?>
                <small class="form-text text-danger help-block"></small>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </div>
            <div class="mt-3 text-center">
                <a href="/login" class="text-muted">Déjà un compte ? Connectez-vous ici</a>
            </div>
            <?php Form::end() ?>
        </div>
    </div>
</div>