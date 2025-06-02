<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class ProcessFormDataEvent extends AbstractFormEvent
{
    public function __construct(
        public array $submittedData,
        public readonly array $formData,
        public readonly ?array $files,
        public readonly array $labels,
        public Form $form,
        public readonly ?int $insertId = null,
    ) {
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