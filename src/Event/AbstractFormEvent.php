<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractFormEvent extends Event
{
    protected Form $form;

    public function getForm(): Form
    {
        return $this->form;
    }
}