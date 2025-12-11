<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\FormModel;

class GetFormEvent extends AbstractFormEvent
{
    private string $buffer;

    public function __construct(private readonly FormModel $formModel, string &$buffer, Form $form)
    {
        $this->buffer = &$buffer;
        $this->form = $form;
    }

    public function getFormModel(): FormModel
    {
        return $this->formModel;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function setBuffer(string $buffer): void
    {
        $this->buffer = $buffer;
    }
}