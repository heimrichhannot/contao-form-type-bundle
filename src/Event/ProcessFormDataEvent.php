<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class ProcessFormDataEvent extends AbstractFormEvent
{
    private array $submittedData;
    private array $formData;
    private ?array $files;
    private array $labels;

    public function __construct(array $submittedData, array $formData, ?array $files, array $labels, Form $form)
    {
        $this->submittedData = $submittedData;
        $this->formData = $formData;
        $this->files = $files;
        $this->labels = $labels;
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