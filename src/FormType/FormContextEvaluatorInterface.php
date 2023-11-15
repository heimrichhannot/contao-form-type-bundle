<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Symfony\Component\HttpFoundation\Request;

interface FormContextEvaluatorInterface
{
    public function __invoke(FormContextConfig $config, Request $request): FormContext;
}