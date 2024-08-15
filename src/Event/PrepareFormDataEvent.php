<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class PrepareFormDataEvent extends AbstractFormEvent
{
    public function __construct(
        private array $data,
        private readonly array $labels,
        private readonly array $fields,
        Form $form
    ) {
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