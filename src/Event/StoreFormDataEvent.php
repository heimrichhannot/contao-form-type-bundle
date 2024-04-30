<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class StoreFormDataEvent extends AbstractFormEvent
{
    public function __construct(private array $data, Form $form, private readonly array $files)
    {
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

    public function getFiles(): array
    {
        return $this->files;
    }
}