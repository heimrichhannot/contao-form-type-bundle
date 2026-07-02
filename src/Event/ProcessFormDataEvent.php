<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class ProcessFormDataEvent extends AbstractFormEvent
{
    public function __construct(
        public array $submittedData,
        public readonly array $formData,
        public ?array $files,
        public array $labels,
        public Form $form,
        public readonly ?int $insertId = null,
    ) {
    }

    /**
     * @deprecated Use property instead
     */
    public function getSubmittedData(): array
    {
        return $this->submittedData;
    }

    /**
     * @deprecated Use property instead
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @deprecated Use property instead
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @deprecated Use property instead
     */
    public function getLabels(): array
    {
        return $this->labels;
    }
}
