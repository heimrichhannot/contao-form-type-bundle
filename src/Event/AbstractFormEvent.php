<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use HeimrichHannot\FormTypeBundle\FormType\FormContext;

abstract class AbstractFormEvent
{
    protected Form $form;
    protected FormContext $formContext;

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getFormContext(): FormContext
    {
        return $this->formContext;
    }

    public function setFormContext(FormContext $formContext): void
    {
        $this->formContext = $formContext;
    }
}