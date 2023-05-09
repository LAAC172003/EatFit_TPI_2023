<?php

namespace Eatfit\Site\Core\Form;

use  Eatfit\Site\Core\Model;

abstract class BaseField
{
    protected Model $model;
    protected string $attribute;
    protected string $type = 'text';
    protected string $placeholder = '';

    /**
     * Field constructor.
     *
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString(): string
    {
        return sprintf('<div class="form-group">
            <label>%s</label>
            %s
            <div class="invalid-feedback">
                %s
            </div>
        </div>', $this->model->getLabel($this->attribute), $this->renderInput(), $this->getErrorMessage());
    }

    abstract protected function renderInput(): string;

    protected function getErrorMessage(): string
    {
        return $this->model->getFirstError($this->attribute);
    }

    /**
     * @param string $type
     * @return BaseField
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }


}
