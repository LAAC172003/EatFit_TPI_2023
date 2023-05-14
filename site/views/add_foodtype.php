<?php use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Core\View;
use Eatfit\Site\Models\FoodType;

/** @var $model FoodType */
/** @var $this View */
$this->title = 'Ajouter un nouveau type'; ?>

<h1>Add New Food Type</h1>

<?php $form = Form::begin("", "post") ?>
<div class="form-group">
    <label for="name">Food Type Name</label>
    <?php echo $form->field($model, 'name') ?>
    <div class="invalid-feedback">
        <?= $model->getFirstError('name') ?>
    </div>
</div>
<input type="submit" class="consume-recipe-button" value="Ajouter">
<?php Form::end() ?>
