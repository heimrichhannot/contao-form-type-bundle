<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\Event;

use Contao\Form;

class StoreFormDataEvent
{
    private array $data;
    private Form $form;

    public function __construct(array $data, Form $form)
    {
        $this->data = $data;
        $this->form = $form;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getForm(): Form
    {
        return $this->form;
    }
}