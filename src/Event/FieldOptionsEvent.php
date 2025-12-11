<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\Widget;
use Symfony\Contracts\EventDispatcher\Event;

class FieldOptionsEvent extends Event
{
    private bool $dirty = false;

    private bool $emptyOption = false;

    private string $emptyOptionLabel = '-';

    public function __construct(private readonly Widget $widget, private readonly Form $form, private array $options = [])
    {
    }

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function addOption(string $value, ?string $label = null): void
    {
        $this->options[] = $this->createOptions($value, $label);
        $this->dirty = true;
    }

    public function createOptions(string $value, ?string $label = null): array
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
     * Set options by values. If a reference array is given, the values will be used as keys and the reference array as values.
     *
     * @param array $values The option values
     * @param array $reference A optional language array
     */
    public function setOptionsByValues(array $values, array $reference = []): void
    {
        $this->setOptions([]);
        foreach ($values as $option) {
            $this->addOption($option, $reference[$option]);
        }

        $this->dirty = true;
    }

    public function setOptionsByKeyValue(array $options): void
    {
        $this->setOptions([]);
        foreach ($options as $key => $value) {
            $this->addOption($key, $value);
        }

        $this->dirty = true;
    }

    public function setEmptyOption(bool $emptyOption, string $label = '-'): void
    {
        $this->emptyOption = $emptyOption;
        $this->emptyOptionLabel = $label;
    }

    public function isEmptyOption(): bool
    {
        return $this->emptyOption;
    }

    public function getEmptyOptionLabel(): string
    {
        return $this->emptyOptionLabel;
    }
}