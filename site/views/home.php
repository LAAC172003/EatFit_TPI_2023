<?php
/** @var $model Recipe */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\FoodType;
use Eatfit\Site\Models\Recipe;

$this->title = 'Accueil';
function renderCarouselWithSearch($category, $recipesByCategory, $carouselId, $searchQuery, $categoryFilter, $foodtypeFilter): void
{
    $recipesToDisplay = array_filter($recipesByCategory, function ($recipe) use ($searchQuery, $categoryFilter, $foodtypeFilter) {
        $titleContainsSearch = empty($searchQuery) || stripos($recipe->recipe_title, $searchQuery) !== false;
        $categoryContainsSearch = empty($searchQuery) || stripos($recipe->categories, $searchQuery) !== false;
        $dateContainsSearch = empty($searchQuery) || stripos($recipe->created_at, $searchQuery) !== false;
        $categoryMatchesFilter = empty($categoryFilter) || $recipe->categories == $categoryFilter;
        $foodtypeMatchesFilter = empty($foodtypeFilter) || stripos($recipe->foodtypes_with_percentages, $foodtypeFilter) !== false;
        return ($titleContainsSearch || $categoryContainsSearch || $dateContainsSearch) && $categoryMatchesFilter && $foodtypeMatchesFilter;
    });
    if (count($recipesToDisplay) === 0) return;
    ?>
    <div class="carousel-container">
        <h1><?= $category->name ?></h1>
        <div class="inner-carousel">
            <div class="track" id="track<?= $carouselId ?>">
                <?php
                foreach ($recipesToDisplay as $recipe) {
                    $recipe->image_paths = !empty($recipe->image_paths) && str_contains($recipe->image_paths, ',') ? array_map('trim', explode(',', $recipe->image_paths)) : array($recipe->image_paths);
                    if (str_contains($recipe->image_paths[0], 'default')) $recipe->image_paths = [explode('_', $recipe->image_paths[0])[1]];
                    ?>
                    <div class="card-container">
                        <div class="container">
                            <div class="row">
                                <div class="menu-item">
                                    <a href="/recipe/detail/<?= $recipe->recipe_id ?>"><img
                                            src="<?= Application::$API_URL . "uploads/" . $recipe->image_paths[0] ?>"
                                            alt="<?= $recipe->recipe_id ?>" class="rounded"></a>
                                    <div>
                                        <h3 style="text-align: left;"><?= $recipe->recipe_title ?></h3>
                                        <p style="text-align: left;"><?= $recipe->recipe_instructions ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="nav" <?php if (count($recipesToDisplay) < 5) echo 'style="display: none;"' ?>>
                <button class="prev" data-index="" id="prev<?= $carouselId ?>" onclick="Prev(<?= $carouselId ?>)"><i
                        class="fas fa-arrow-left fa-2x"></i></button>
                <button class="next" data-index="0" id="next<?= $carouselId ?>" onclick="Next(<?= $carouselId ?>)"><i
                        class="fas fa-arrow-right fa-2x"></i></button>
            </div>
        </div>
    </div>
    <?php
}

$form = Form::begin('', "post");
?>
    <div class="search-bar">
        <input type="text" id="search-input" name="search" placeholder="Rechercher des recettes..."/>
        <button id="search-button">Rechercher</button>
    </div>
    <div class="filter-container">
        <select id="category-filter" name="category-filter">
            <option value="">Toutes les catégories</option>
            <?php foreach ($model->getCategories()->value as $category) { ?>
                <option value="<?= $category->name ?>"><?= $category->name ?></option>
            <?php } ?>
        </select>

        <select id="foodtype-filter" name="foodtype-filter">
            <option value="">Tous les types d'aliments</option>
            <?php foreach (FoodType::getFoodTypes()->value as $foodtype) { ?>
                <option value="<?= $foodtype->name ?>"><?= $foodtype->name ?></option>
            <?php } ?>
        </select>

        <button id="filter-button">Filtrer</button>
    </div>

<?php
Form::end();
$j = 0;
$searchQuery = $_POST['search'] ?? '';
$categoryFilter = $_POST['category-filter'] ?? '';
$foodtypeFilter = $_POST['foodtype-filter'] ?? '';
foreach ($model->getCategories()->value as $category) {
    $recipesByCategory = $model->getRecipeByFilter("category", $category->name)->value;
    renderCarouselWithSearch($category, $recipesByCategory, $j, $searchQuery, $categoryFilter, $foodtypeFilter);
    $j++;
}

