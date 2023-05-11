<?php
/** @var $model Recipe */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\Recipe;

$this->title = 'Ajouter une recette';
?>

<main>
    <div class="container py-5">
        <h2 class="text-center mb-4">Ajouter une nouvelle recette</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php $form = Form::begin("", "post", ["enctype" => "multipart/form-data"]) ?>
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
                        'Petit déjeuner' => 'Petit déjeuner',
                        'medium' => 'Moyen',
                        'hard' => 'Difficile'
                    ]) ?>
                </div>
                <div class="form-group">
                    <label for="file-input">Image: </label>
                    <br>
                    <?php
                    $acceptTypes = array();
                    foreach (Application::$ALLOWED_IMAGE_EXTENSIONS as $extension) $acceptTypes[] = 'image/' . $extension;
                    $acceptValue = implode(',', $acceptTypes);
                    ?>
                    <input type="file" multiple name="file-input[]" accept="<?= $acceptValue; ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Ajouter la recette</button>
                <?php Form::end(); ?>
            </div
        </div>
    </div>
</main>
