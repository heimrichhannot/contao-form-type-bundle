<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType;

use Contao\FormModel;
use HeimrichHannot\FormgeneratorTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\Event\StoreFormDataEvent;
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

    public function getDefaultFields(FormModel $formModel): array
    {
        return [];
    }

    public function onPrepareFormData(PrepareFormDataEvent $event): void
    {
    }

    public function onStoreFormData(StoreFormDataEvent $event): void
    {
    }
}