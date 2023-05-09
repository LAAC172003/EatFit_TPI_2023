<?php
/** @var $model Recipe */
///** @var $difficulty array */
///** @var $categories array */


use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\Recipe;

$this->title = 'Ajouter une recette';
?>

<main>
    <div class="container py-5">
        <h2 class="text-center mb-4">Ajouter une nouvelle recette</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php $form = Form::begin("", "post") ?>
                <form class="recipe-form" method="post" action="/add-recipe" enctype="multipart/form-data">
                    <div class="form-group">
                        <?php echo $form->field($model, 'title') ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, 'preparation_time')->numberField() ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, 'difficulty')->selectField([
                            'easy' => 'Facile',
                            'medium' => 'Moyen',
                            'hard' => 'Difficile'
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, 'instructions')->textarea() ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, 'calories')->numberField() ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, 'category')->selectField([
                            'easy' => 'Petit déjeuner',
                            'medium' => 'Moyen',
                            'hard' => 'Difficile'
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->field($model, "image")->fileField() ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Ajouter la recette</button>
                    <?php Form::end(); ?>
            </div
        </div>
    </div>
</main>