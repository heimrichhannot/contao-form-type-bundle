<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class StoreFormDataEvent extends AbstractFormEvent
{
    private array $data;
    private array $files;

    public function __construct(array $data, Form $form, array $files)
    {
        $this->data = $data;
        $this->form = $form;
        $this->files = $files;
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