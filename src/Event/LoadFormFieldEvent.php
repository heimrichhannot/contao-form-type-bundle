<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\Widget;
use HeimrichHannot\FormTypeBundle\FormType\FormContext;
use Symfony\Contracts\EventDispatcher\Event;

class LoadFormFieldEvent extends Event
{
    public function __construct(
        private Widget $widget,
        private readonly string $formId,
        private array $formData,
        private readonly Form $form,
        private readonly FormContext $formContext
    ) {}

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    /**
     * @deprecated Not necessary. Use getWidget() instead
     */
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

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getFormContext(): FormContext
    {
        return $this->formContext;
    }
}