<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class ProcessFormDataEvent extends AbstractFormEvent
{
    public function __construct(private readonly array $submittedData, private readonly array $formData, private readonly ?array $files, private readonly array $labels, Form $form)
    {
        $this->form = $form;
    }

    public function getSubmittedData(): array
    {
        return $this->submittedData;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }
}