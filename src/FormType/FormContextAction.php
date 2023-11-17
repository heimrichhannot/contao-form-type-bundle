<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

enum FormContextAction: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case CLONE = 'clone';

    public function equals(mixed $value): bool
    {
        if ($value instanceof self) {
            return $this === $value;
        }

        if (is_string($value)) {
            return $this->value === $value || $this->name === $value;
        }

        return false;
    }
}