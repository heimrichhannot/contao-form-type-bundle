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
}