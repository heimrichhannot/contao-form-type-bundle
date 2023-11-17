<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\Model;

trait FormContextTrait
{
    private FormContextAction $formContextAction = FormContextAction::CREATE;
    private ?Model $formContextModel = null;

    public function getFormContextAction(): FormContextAction
    {
        return $this->formContextAction;
    }

    public function setFormContextAction(FormContextAction $formContextAction): void
    {
        $this->formContextAction = $formContextAction;
    }

    public function getFormContextModel(): ?Model
    {
        return $this->formContextModel;
    }

    public function setFormContextModel(?Model $formContextModel): void
    {
        $this->formContextModel = $formContextModel;
    }

    abstract public function evaluateFormContext(): void;

    final public function isContextEdit(): bool
    {
        return FormContextAction::EDIT === $this->formContextAction;
    }

    final public function isContextCreate(): bool
    {
        return FormContextAction::CREATE === $this->formContextAction;
    }

}