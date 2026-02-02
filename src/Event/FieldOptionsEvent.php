<?php

namespace HeimrichHannot\FormTypeBundle\Event;

use Contao\Form;
use Contao\Widget;
use Symfony\Contracts\EventDispatcher\Event;

class FieldOptionsEvent extends Event
{
    private bool $dirty = false;
    private bool $emptyOption = false;
    private bool $grouped = false;
    private bool $sorted = false;

    private string $emptyOptionLabel = '-';

    public function __construct(
        private readonly Widget $widget,
        private readonly Form   $form,
        private array           $options = []
    ) {}

    public function getWidget(): Widget
    {
        return $this->widget;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function addOption(string $value, ?string $label = null, ?string $group = null): self
    {
        $this->options[] = $this->createOptions($value, $label, $group);
        $this->dirty = true;
        return $this;
    }

    public function createOptions(string $value, ?string $label = null, ?string $group = null): array
    {
        if (!$label) {
            $label = $value;
        }

        return [
            "group" => $group,
            "value" => $value,
            "label" => $label,
        ];
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        $this->dirty = true;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isDirty(): bool
    {
        return $this->dirty;
    }

    public function isGrouped(): bool
    {
        return $this->grouped;
    }

    public function setGrouped(bool $grouped): self
    {
        $this->grouped = $grouped;
        return $this;
    }

    public function isSorted(): bool
    {
        return $this->sorted;
    }

    public function setSorted(bool $sorted): self
    {
        $this->sorted = $sorted;
        return $this;
    }

    /**
     * Set options by values. If a reference array is given, the values will be used as keys and the reference array as
     * values.
     *
     * @param array $values The option values
     * @param array $reference An optional language array
     */
    public function setOptionsByValues(array $values, array $reference = []): self
    {
        $this->setOptions([]);
        foreach ($values as $option) {
            $this->addOption($option, $reference[$option]);
        }

        $this->dirty = true;
        return $this;
    }

    public function setOptionsByKeyValue(array $options): self
    {
        $this->setOptions([]);
        foreach ($options as $key => $value) {
            $this->addOption($key, $value);
        }

        $this->dirty = true;
        return $this;
    }

    public function setEmptyOption(bool $emptyOption, string $label = '-'): self
    {
        $this->emptyOption = $emptyOption;
        $this->emptyOptionLabel = $label;
        return $this;
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