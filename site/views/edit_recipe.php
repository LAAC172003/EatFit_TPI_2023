<?php
/** @var $model Recipe */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\FoodType;
use Eatfit\Site\Models\Recipe;

$this->title = 'Modifier une recette';
?>

<main>
    <div class="container py-5">
        <h2 class="text-center mb-4">Modifier la recette</h2>
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
                    <?php echo $form->field($model, 'calories')->numberField() ?>
                </div>
                <div class="form-group">
                    <?php echo $form->field($model, 'difficulty')->selectField([
                        'facile' => 'Facile',
                        'moyen' => 'Moyen',
                        'difficile' => 'Difficile'
                    ]) ?>
                </div>
                <div class="form-group">
                    <?php echo $form->field($model, 'instructions')->textarea() ?>
                </div>
                <div class="form-group">
                    <?php
                    $categories = array();
                    foreach ($model->getCategories()->value as $category) $categories[$category->name] = $category->name;
                    echo $form->field($model, 'category')->selectField($categories) ?>
                </div>
                <div id="foodtype-container"></div>

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

                <button type="button" onclick="addFoodTypeField()">Ajouter un type de nourriture</button>
                <button type="button" onclick="removeFoodTypeField()">Supprimer le dernier type de nourriture</button>

                <button type="submit" class="btn btn-primary btn-block">Mettre à jour la recette</button>

                <?php Form::end(); ?>
            </div>
        </div>
    </div>
</main>

<script>
    function addFoodTypeField() {
        var container = document.getElementById('foodtype-container');
        var div = document.createElement('div');
        div.innerHTML = `
            <div class="form-group">
                <label for="foodtype">Type de nourriture :</label>
                <select name="foodtype[]" class="form-control">
                    <?php
        foreach (FoodType::getFoodTypes()->value as $foodType) {
            echo "<option value='{$foodType->name}'>{$foodType->name}</option>";
        }
        ?>
                </select>
            </div>
            <div class="form-group">
                <label for="percentage">Pourcentage :</label>
                <input type="number" name="percentage[]" class="form-control">
            </div>
        `;
        container.appendChild(div);
    }

    function removeFoodTypeField() {
        var container = document.getElementById('foodtype-container');
        if (container.children.length > 0) {
            container.removeChild(container.lastChild);
        }
    }

</script>
