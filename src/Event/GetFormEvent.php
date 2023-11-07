<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\FormModel;

class GetFormEvent
{
    private FormModel $formModel;
    private string $buffer;
    private Form $form;

    public function __construct(FormModel $formModel, string &$buffer, Form $form)
    {
        $this->formModel = $formModel;
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

    public function getForm(): Form
    {
        return $this->form;
    }
}