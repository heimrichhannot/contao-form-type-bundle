<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType;

interface FormgeneratorTypeInterface
{
    /**
     * Adjust the backend dca for the current formgenerator type.
     *
     * @return void
     */
    public function onload(): void;
}