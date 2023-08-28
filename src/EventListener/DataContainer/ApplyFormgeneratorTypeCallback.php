<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;

/**
 * @Callback(table="tl_form_field", target="config.onload")
 */
class ApplyFormgeneratorTypeCallback
{
    public function __invoke(DataContainer $dc = null): void
    {

    }

}