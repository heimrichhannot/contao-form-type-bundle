<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType;

use Symfony\Component\DependencyInjection\Container;

abstract class AbstractFormgeneratorType implements FormgeneratorTypeInterface
{
    public function getType(): string
    {
        $className = ltrim(strrchr(static::class, '\\'), '\\');

        if ('FormgeneratorType' === substr($className, -17)) {
            $className = substr($className, 0, -17);
        } elseif ('Type' === substr($className, -4)) {
            $className = substr($className, 0, -4);
        }

        return Container::underscore($className);
    }

    public function getDefaultFields(): array
    {
        return [];
    }
}