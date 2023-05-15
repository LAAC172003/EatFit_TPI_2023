<?php
/** @var $user array */

/** @var $model History */


use Eatfit\Site\Models\History;

$this->title = 'Historique';
?>

<h1>Historique des recettes consommées</h1>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Identifiant de la recette</th>
            <th>Titre de la recette</th>
            <th>Date de la réalisation</th>
            <th>Moment de la consommation</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $history = $model->getHistory()->value;
        if ($history == null) {
            echo "<tr><td colspan='5'>Aucune recette consommée</td></tr>";
        } else
            foreach ($history as $item): ?>
                <tr>
                    <td data-column="Identifiant de la recette"><?php echo $item->idRecipe; ?></td>
                    <td data-column="Titre de la recette"><?php echo $item->title; ?></td>
                    <td data-column="Date de la réalisation"><?php echo $item->created_at; ?></td>
                    <td data-column="Moment de la consommation"><?php echo $item->consumption_date; ?></td>
                    <td data-column="Actions">
                        <a href="/history/delete/<?php echo $item->idConsumedRecipe; ?>">Supprimer cette recette</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<a href="/history/delete">Supprimer tout l'historique</a>
