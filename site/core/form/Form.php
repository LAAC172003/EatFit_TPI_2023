<?php

namespace Eatfit\Site\Core\Form;

use Eatfit\Site\Core\Model;

class Form
{
    /**
     * Démarre un formulaire HTML.
     *
     * @param string $action L'action du formulaire (URL de destination des données).
     * @param string $method La méthode HTTP à utiliser (GET, POST, etc.).
     * @param array $options Options supplémentaires pour les attributs du formulaire.
     * @return Form Une instance de la classe Form.
     */
    public static function begin($action, $method, $options = [])
    {
        $attributes = [];
        foreach ($options as $key => $value) {
            $attributes[] = "$key=\"$value\"";
        }
        echo sprintf('<form action="%s" method="%s" %s>', $action, $method, implode(" ", $attributes));
        return new Form();
    }

    /**
     * Ferme le formulaire HTML.
     *
     * @return void
     */
    public static function end()
    {
        echo '</form>';
    }

    /**
     * Crée un champ de formulaire pour un modèle et un attribut spécifiés.
     *
     * @param Model $model Le modèle associé au champ de formulaire.
     * @param string $attribute L'attribut du modèle correspondant au champ de formulaire.
     * @return Field Une instance de la classe Field pour le champ de formulaire créé.
     */
    public function field(Model $model, $attribute): Field
    {
        return new Field($model, $attribute);
    }
}