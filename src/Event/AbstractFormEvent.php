<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

abstract class AbstractFormEvent
{
    protected Form $form;

    public function getForm(): Form
    {
        return $this->form;
    }
}