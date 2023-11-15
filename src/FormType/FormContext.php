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
        private FormContextAction $context,
        private ?Model            $model = null
    )
    {
    }

    public function getContext(): FormContextAction
    {
        return $this->context;
    }

    public function setContext(FormContextAction $context): void
    {
        $this->context = $context;
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
        return FormContextAction::EDIT === $this->context;
    }

    public function isCreateContext(): bool
    {
        return FormContextAction::CREATE === $this->context;
    }
}