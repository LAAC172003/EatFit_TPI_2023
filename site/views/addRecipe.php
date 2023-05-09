<main>
    <div class="container py-5">
        <h2 class="text-center mb-4">Ajouter une nouvelle recette</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form class="recipe-form" method="post" action="/add-recipe" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="id-recipe">ID de la recette<span class="obligatoire">*</span></label>
                        <input type="text" class="form-control" id="id-recipe" name="id-recipe" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Titre<span class="obligatoire">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="preparation-time">Temps de préparation<span class="obligatoire">*</span></label>
                        <input type="text" class="form-control" id="preparation-time" name="preparation-time" required>
                    </div>
                    <div class="form-group">
                        <label for="difficulty">Difficulté<span class="obligatoire">*</span></label>
                        <select class="form-control" id="difficulty" name="difficulty">
                            <option>Facile</option>
                            <option>Moyen</option>
                            <option>Difficile</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="instructions">Instructions<span class="obligatoire">*</span></label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="5"
                                  required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="calories">Calories</label>
                        <input type="number" class="form-control" id="calories" name="calories">
                    </div>
                    <div class="form-group">
                        <label for="created-at">Date de création<span class="obligatoire">*</span></label>
                        <input type="date" class="form-control" id="created-at" name="created-at" required>
                    </div>
                    <div class="form-group">
                        <label for="creator-name">Nom du créateur<span class="obligatoire">*</span></label>
                        <input type="text" class="form-control" id="creator-name" name="creator-name" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image<span class="obligatoire">*</span></label>
                        <input type="file" class="form-control-file" id="image" name="image" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Catégorie<span class="obligatoire">*</span></label>
                        <select class="form-control" id="category" name="category">
                            <option>Petit déjeuner</option>
                            <option>Déjeuner</option>
                            <option>Dîner</option>
                            <option>Dessert</option>
                            <option>Snack</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Ajouter la recette</button>
                </form>
            </div
        </div>
    </div>
</main>
