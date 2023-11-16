<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\Form;

class FormTypeCollection
{
    private array $types = [];

    public function addType(AbstractFormType|FormTypeInterface $type): void
    {
        $this->types[$type->getType()] = $type;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(Form|string $formOrName): AbstractFormType|FormTypeInterface|null
    {
        return is_string($formOrName)
            ? $this->getTypeByName($formOrName)
            : $this->getTypeOfForm($formOrName);
    }

    public function getTypeByName(string $type): AbstractFormType|FormTypeInterface|null
    {
        return $this->types[$type] ?? null;
    }

    public function getTypeOfForm(Form $form): AbstractFormType|FormTypeInterface|null
    {
        if ($form->formType && $formType = $this->getTypeByName($form->formType)) {
            return $formType;
        }
        return null;
    }
}