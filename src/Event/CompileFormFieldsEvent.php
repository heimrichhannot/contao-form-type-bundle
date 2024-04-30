<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class CompileFormFieldsEvent extends AbstractFormEvent
{
    public function __construct(private array $fields, private readonly string $formId, Form $form)
    {
        $this->form = $form;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }
}