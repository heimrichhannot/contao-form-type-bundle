<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType;

class FormgeneratorTypeCollection
{
    private array $types = [];

    public function addType(FormgeneratorTypeInterface $type): void
    {
        $this->types[$type->getType()] = $type;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(string $type): ?FormgeneratorTypeInterface
    {
        return $this->types[$type] ?? null;
    }
}