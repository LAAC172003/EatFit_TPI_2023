<?php
/** @var $user array */

/** @var $model ProfileModel */

/** @var $this View */

use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Core\View;
use Eatfit\Site\Models\ProfileModel;

$this->title = 'Profile';
?>
<h2 class="text-uppercase text-center text-secondary mb-0 ">Votre profil</h2>
<hr class="star-dark mb-5 border-dark">
<div class="ms-0 me-0 row">
    <div class="col-lg-8 mx-auto">
        <?php $form = Form::begin('/profile/update', 'post', ['id' => 'updateProfileForm']) ?>
        <div class="control-group mb-3 row">
            <label class="form-label col label-dark" style="margin: auto;">Email : <?= $user->email ?></label>
            <button class="consume-recipe-button" type="button" onclick="edit('email')">Modifier</button>
        </div>
        <div class="control-group row" id="email"></div>
        <div class="control-group mb-3 row">
            <label class="form-label col label-dark" style="margin: auto;">Username
                : <?= $user->username ?></label>
            <button class="consume-recipe-button" type="button" onclick="edit('username')">Modifier</button>
        </div>
        <div class="control-group row" id="username"></div>
        <div class="control-group mb-3 row">
            <label class="form-label col label-dark" style="margin: auto;">Mot de
                passe : </label>
            <button class="consume-recipe-button" type="button" onclick="edit('password')">Modifier</button>
        </div>
        <div class="control-group row" id="password"></div>
        <div class="control-group mt-3 row">
            <button class="consume-recipe-button" id="sendMessageButton" type="submit">Valider</button>
        </div>
        <?php Form::end() ?>

        <?php $form = Form::begin('/profile/delete', 'post') ?>
        <div class="control-group mt-3 row">
            <button class="btn btn-danger btn-xl" id="deleteAccountButton" type="submit">Supprimer le compte</button>
        </div>
        <?php Form::end() ?>
    </div>
</div>
<script>
    function edit(id) {
        let div = document.getElementById(id);
        if (id === "password") {
            if (div.innerHTML === "") {
                div.innerHTML += '<input class="form-control mt-2" type="password" name="password" placeholder="Nouveau mot de passe">';
                div.innerHTML += '<input class="form-control mt-2" type="password" name="confirm_password" placeholder="Confirmer le mot de passe">';
                div.innerHTML += '<div class="invalid-feedback"></div>';
            } else div.innerHTML = "";
        } else if (id === "username") {
            let placeholder = "Nouveau pseudo";
            if (div.innerHTML === "") div.innerHTML = '<input class="form-control mb-3" name="' + id + '" placeholder="' + placeholder + '" type="text">';
            else div.innerHTML = "";
        } else if (id === "email") {
            let placeholder = "Nouveau email";
            if (div.innerHTML === "") div.innerHTML = '<input class="form-control mb-3" name="' + id + '" placeholder="' + placeholder + '" type="email">';
            else div.innerHTML = "";
        }
    }
</script>