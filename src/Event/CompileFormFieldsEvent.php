<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class CompileFormFieldsEvent extends AbstractFormEvent
{
    private array $fields;
    private string $formId;

    public function __construct(array $fields, string $formId, Form $form)
    {
        $this->fields = $fields;
        $this->formId = $formId;
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

    public function getForm(): Form
    {
        return $this->form;
    }

}