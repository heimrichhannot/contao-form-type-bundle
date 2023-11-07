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

/**
 * @internal Do not use this class in your code. It is only public for reasons of backward compatibility.
 * @deprecated Use AbstractFormType instead.
 */
interface FormTypeInterface
{
    /**
     * Return the type name of the form type.
     */
    public function getType(): string;

    /**
     * Adjust the backend dca for the current form type.
     */
    public function onload(DataContainer $dataContainer, FormModel $formModel): void;

    /**
     * Return the default fields for the current form type. Will be used for the first time wizard
     *
     * Example:
     * ```php
     * return [
     *  [
     *    'type' => 'text',
     *    'name' => 'title',
     *    'mandatory' => '1',
     *  ],
     * ];
     * ```
     */
    public function getDefaultFields(FormModel $formModel): array;

    public function onPrepareFormData(PrepareFormDataEvent $event): void;

    public function onStoreFormData(StoreFormDataEvent $event): void;

    public function onProcessFormData(ProcessFormDataEvent $event): void;

    public function onValidateFormField(ValidateFormFieldEvent $event): void;

    public function onCompileFormFields(CompileFormFieldsEvent $event): void;

    public function onLoadFormField(LoadFormFieldEvent $event): void;

    public function onGetForm(GetFormEvent $event): void;
}