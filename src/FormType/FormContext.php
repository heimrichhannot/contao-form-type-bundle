<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\Model;

class FormContext
{
    public static function edit(Model|null $model): self
    {
        return new static(FormContextAction::EDIT, $model);
    }

    public static function create(): self
    {
        return new static(FormContextAction::CREATE);
    }

    public function __construct(
        private FormContextAction $action,
        private ?Model            $model = null
    )
    {
    }

    public function getAction(): FormContextAction
    {
        return $this->action;
    }

    public function setAction(FormContextAction $action): void
    {
        $this->action = $action;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    public function isEditContext(): bool
    {
        return FormContextAction::EDIT === $this->action;
    }

    public function isCreateContext(): bool
    {
        return FormContextAction::CREATE === $this->action;
    }
}