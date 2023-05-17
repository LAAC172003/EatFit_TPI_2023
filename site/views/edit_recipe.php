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
                    echo $form->field($model, 'categories')->selectField($categories) ?>
                </div>
                <div id="foodtype-container"></div>
                <table>
                    <tr>
                        <th>Type de nourriture</th>
                        <th>Pourcentage</th>
                    </tr>
                    <?php foreach (FoodType::getFoodTypes()->value as $foodType):
                        ?>
                        <tr>
                            <td><?= $foodType->name ?></td>
                            <td>
                                <input type="number" name="percentage[<?= $foodType->name ?>]" class="form-control"
                                       value="<?= $model->foodType[trim($foodType->name)] ?? '' ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="form-group">
                    <br>
                    <div class="image-container">
                        <?php foreach ($model->image as $image):
                            if (str_contains($image, 'default')) continue;
                            ?>
                            <div class="image-wrapper">
                                <img src="<?= Application::$API_URL . "uploads/" . $image ?>" alt="<?= $image ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <br>
                    <label class="label-dark" for="file-input">Ajouter une image: </label>
                    <br>
                    <?php
                    $acceptTypes = array();
                    foreach (Application::$ALLOWED_IMAGE_EXTENSIONS as $extension) $acceptTypes[] = 'image/' . $extension;
                    $acceptValue = implode(',', $acceptTypes);
                    ?>
                    <label for="default">Image par défaut
                        <input type="checkbox" name="default">
                    </label>
                    <br>
                    <input type="file" multiple name="file-input[]" accept="<?= $acceptValue; ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Mettre à jour la recette</button>
                <?php Form::end(); ?>
            </div>
        </div>
    </div>
</main>