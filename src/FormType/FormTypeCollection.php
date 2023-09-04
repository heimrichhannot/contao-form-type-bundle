<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

class FormTypeCollection
{
    private array $types = [];

    public function addType(FormTypeInterface $type): void
    {
        $this->types[$type->getType()] = $type;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(string $type): ?FormTypeInterface
    {
        return $this->types[$type] ?? null;
    }
}