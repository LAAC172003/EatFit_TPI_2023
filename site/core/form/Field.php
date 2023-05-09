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
     * Field constructor.
     *
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function textarea(): self
    {
        return $this->setType(self::TYPE_TEXTAREA);
    }

    public function checkbox(): self
    {
        return $this->setType(self::TYPE_CHECKBOX);
    }

    public function radio(): self
    {
        return $this->setType(self::TYPE_RADIO);
    }

    public function passwordField(): self
    {
        return $this->setType(self::TYPE_PASSWORD);
    }

    public function numberField(): self
    {
        return $this->setType(self::TYPE_NUMBER);
    }

    public function selectField(array $options): self
    {
        $this->type = 'select';
        $this->options = $options;
        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

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
            $optionsString = '';
            foreach ($this->options as $value => $label) {
                $optionsString .= sprintf('<option value="%s">%s</option>', $value, $label);
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
     * @param string $placeholder
     * @return Field
     */
    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function fileField(): self
    {
        return $this->setType(self::TYPE_FILE);
    }

}
