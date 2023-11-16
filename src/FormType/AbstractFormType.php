<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\DataContainer;
use Contao\FormModel;
use HeimrichHannot\FormTypeBundle\Event\CompileFormFieldsEvent;
use HeimrichHannot\FormTypeBundle\Event\GetFormEvent;
use HeimrichHannot\FormTypeBundle\Event\LoadFormFieldEvent;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ValidateFormFieldEvent;
use Symfony\Component\DependencyInjection\Container;

abstract class AbstractFormType implements FormTypeInterface
{
    public function getType(): string
    {
        $className = ltrim(strrchr(static::class, '\\'), '\\');

        if ('FormType' === substr($className, -8)) {
            $className = substr($className, 0, -8);
        } elseif ('Type' === substr($className, -4)) {
            $className = substr($className, 0, -4);
        }

        return Container::underscore($className);
    }

    public function getDefaultFields(FormModel $formModel): array
    {
        return [];
    }

    public function getFormContext(): FormContext
    {
        return FormContext::create();
    }

    abstract public function onload(DataContainer $dataContainer, FormModel $formModel): void;

    public function onPrepareFormData(PrepareFormDataEvent $event): void
    {
    }

    public function onStoreFormData(StoreFormDataEvent $event): void
    {
    }

    public function onProcessFormData(ProcessFormDataEvent $event): void
    {
    }

    public function onValidateFormField(ValidateFormFieldEvent $event): void
    {
    }

    public function onCompileFormFields(CompileFormFieldsEvent $event): void
    {
    }

    public function onLoadFormField(LoadFormFieldEvent $event): void
    {
    }

    public function onGetForm(GetFormEvent $event): void
    {
    }
}