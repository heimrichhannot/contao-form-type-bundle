<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType;

use Contao\DataContainer;
use Contao\FormModel;

interface FormgeneratorTypeInterface
{
    /**
     * Return the type name of the formgenerator type.
     */
    public function getType(): string;

    /**
     * Adjust the backend dca for the current formgenerator type.
     */
    public function onload(DataContainer $dataContainer, FormModel $formModel): void;

    /**
     * Return the default fields for the current formgenerator type. Will be used for the first time wizard
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
}