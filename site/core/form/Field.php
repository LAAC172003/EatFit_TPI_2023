<?php

namespace Eatfit\Site\Core\Form;

use Eatfit\Site\Core\Model;

class Field extends BaseField
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_NUMBER = 'number';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE = 'file';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    protected array $attributes = [];
    private array $options;

    /**
     * Constructeur de la classe Field.
     *
     * @param Model $model Le modèle associé au champ.
     * @param string $attribute L'attribut du modèle correspondant au champ.
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }


    /**
     * Définit le champ comme un champ textarea.
     *
     * @return Field L'instance du champ Field.
     */
    public function textarea(): self
    {
        return $this->setType(self::TYPE_TEXTAREA);
    }

    /**
     * Définit le champ comme un champ checkbox.
     *
     * @return Field L'instance du champ Field.
     */
    public function checkbox(): self
    {
        return $this->setType(self::TYPE_CHECKBOX);
    }

    /**
     * Définit le champ comme un champ radio.
     *
     * @return Field L'instance du champ Field.
     */
    public function radio(): self
    {
        return $this->setType(self::TYPE_RADIO);
    }

    /**
     * Définit le champ comme un champ de mot de passe.
     *
     * @return Field L'instance du champ Field.
     */
    public function passwordField(): self
    {
        return $this->setType(self::TYPE_PASSWORD);
    }

    /**
     * Définit le champ comme un champ de nombre.
     *
     * @return Field L'instance du champ Field.
     */
    public function numberField(): self
    {
        return $this->setType(self::TYPE_NUMBER);
    }

    /**
     * Définit le champ comme un champ select avec les options spécifiées.
     *
     * @param array $options Les options du champ select.
     * @return Field L'instance du champ Field.
     */
    public function selectField(array $options): self
    {
        $this->type = 'select';
        $this->options = $options;
        return $this;
    }

    /**
     * Définit les attributs supplémentaires du champ.
     *
     * @param array $attributes Les attributs du champ.
     * @return Field L'instance du champ Field.
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Rendu de l'élément de champ.
     *
     * @return string Le rendu de l'élément de champ spécifique.
     */
    public function renderInput(): string
    {
        $placeholderText = !empty($this->placeholder) ? sprintf('placeholder="%s"', $this->placeholder) : '';
        $additionalAttributes = '';
        foreach ($this->attributes as $attribute => $value) {
            $additionalAttributes .= sprintf(' %s="%s"', $attribute, $value);
        }

        if ($this->type === self::TYPE_TEXTAREA) {
            return sprintf('<textarea class="form-control%s" name="%s" %s %s>%s</textarea>',
                $this->model->hasError($this->attribute) ? ' is-invalid' : '',
                $this->attribute,
                $placeholderText,
                $additionalAttributes,
                $this->model->{$this->attribute}
            );
        }
        if ($this->type === 'select') {
            $optionsString = '<option value="">Choisissez une option</option>';  // option par défaut
            foreach ($this->options as $value => $label) {
                $selected = ($this->model->{$this->attribute} === $value) ? ' selected' : '';
                $optionsString .= sprintf('<option value="%s"%s>%s</option>', $value, $selected, $label);
            }

            return sprintf('<select class="form-control%s" name="%s" %s %s>%s</select>',
                $this->model->hasError($this->attribute) ? ' is-invalid' : '',
                $this->attribute,
                $placeholderText,
                $additionalAttributes,
                $optionsString
            );
        }

        return sprintf('<input type="%s" class="form-control%s" name="%s" value="%s" %s %s>',
            $this->type,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->attribute,
            $this->model->{$this->attribute},
            $placeholderText,
            $additionalAttributes
        );

    }


    /**
     * Définit le texte d'espace réservé pour le champ.
     *
     * @param string $placeholder Le texte d'espace réservé.
     * @return Field L'instance du champ Field.
     */
    public
    function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public
    function fileField(): self
    {
        return $this->setType(self::TYPE_FILE);
    }

}
