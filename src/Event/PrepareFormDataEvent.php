<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class PrepareFormDataEvent extends AbstractFormEvent
{
    private array $data;
    private array $labels;
    private array $fields;

    public function __construct(array $data, array $labels, array $fields, Form $form)
    {
        $this->data = $data;
        $this->labels = $labels;
        $this->fields = $fields;
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

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}