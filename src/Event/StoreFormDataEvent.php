<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\Event;

use Contao\Form;

class StoreFormDataEvent
{
    private array $data;
    private Form $form;
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

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}