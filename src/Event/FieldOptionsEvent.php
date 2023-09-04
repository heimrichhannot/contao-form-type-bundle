<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\Widget;
use Symfony\Contracts\EventDispatcher\Event;

class FieldOptionsEvent extends Event
{
    private Widget $widget;
    private Form $form;
    private array $options;
    private bool $dirty = false;

    public function __construct(Widget $widget, Form $form, array $options = [])
    {
        $this->widget = $widget;
        $this->form = $form;
        $this->options = $options;
    }

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function addOption(string $value, string $label = null): void
    {
        $this->options[] = $this->createOptions($value, $label);
        $this->dirty = true;
    }

    public function createOptions(string $value, string $label = null): array
    {
        if (!$label) {
            $label = $value;
        }

        return [
            "value" => $value,
            "label" => $label,
        ];
    }

    public function setOptions(array $options = []): void
    {
        $this->options = $options;
        $this->dirty = true;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isDirty(): bool
    {
        return $this->dirty;
    }

    /**
     * Helper method for usage together with the dca field reference value.
     *
     * @param array $values The option values
     * @param array $reference A language array
     * @return void
     */
    public function setOptionsWithReference(array $values, array $reference): void
    {
        $this->setOptions([]);
        foreach ($values as $option) {
            $this->addOption($option, $reference[$option]);
        }

        $this->dirty = true;
    }
}