<?php
/** @var $model Rating */

use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\Rating;

//var_dump($model);

?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="form-label label-dark">Modifier l'évaluation de la recette</h2>
            <?php $form = Form::begin("", "post") ?>
            <div class="mb-3">
                <?= $form->field($model, "score")->numberField() ?>
            </div>
            <div class="mb-3">
                <?= $form->field($model, "comment")->textarea() ?>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary-custom btn-lg btn-block">Mettre à jour l'évaluation
                </button>
            </div>
            <?php Form::end() ?>
        </div>
    </div>
</div>
