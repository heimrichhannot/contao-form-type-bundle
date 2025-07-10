<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;

class PrepareFormDataEvent
{
    public function __construct(
        public array $data,
        public array $labels,
        public readonly array $fields,
        public readonly Form $form
    ) {
    }

    /**
     * @deprecated
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @deprecated
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @deprecated
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @deprecated
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @deprecated
     */
    public function getForm(): Form
    {
        return $this->form;
    }


}