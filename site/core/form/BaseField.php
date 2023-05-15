<?php

namespace Eatfit\Site\Core\Form;

use Eatfit\Site\Core\Model;

abstract class BaseField
{
    protected Model $model;
    protected string $attribute;
    protected string $type = 'text';
    protected string $placeholder = '';

    /**
     * Constructeur de la classe BaseField.
     *
     * @param Model $model Le modèle associé au champ.
     * @param string $attribute L'attribut du modèle correspondant au champ.
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    /**
     * Méthode magique __toString() - renvoie le champ sous forme de chaîne de caractères.
     *
     * @return string Le champ HTML rendu.
     */
    public function __toString(): string
    {
        return sprintf('<div class="form-label label-dark">
            <label>%s</label>
            %s
            <div class="invalid-feedback">
                %s
            </div>
        </div>', $this->model->getLabel($this->attribute), $this->renderInput(), $this->getErrorMessage());
    }

    /**
     * Méthode abstraite renderInput() - rendu de l'élément de champ.
     *
     * @return string Le rendu de l'élément de champ spécifique.
     */
    abstract protected function renderInput(): string;

    /**
     * Obtient le message d'erreur associé au champ.
     *
     * @return string Le message d'erreur du champ.
     */
    protected function getErrorMessage(): string
    {
        return $this->model->getFirstError($this->attribute);
    }

    /**
     * Définit le type du champ.
     *
     * @param string $type Le type du champ.
     * @return BaseField L'instance de BaseField.
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }


}
