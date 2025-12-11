<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\Widget;

class ValidateFormFieldEvent extends AbstractFormEvent
{
    public function __construct(
        private Widget $widget,
        private readonly string $formId,
        private array $formData,
        Form $form
    ) {
        $this->form = $form;
    }

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function setWidget(Widget $widget): void
    {
        $this->widget = $widget;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function setFormData(array $formData): void
    {
        $this->formData = $formData;
    }
}