<?php

namespace HeimrichHannot\FormgeneratorTypeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotFormgeneratorTypeBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}