<?php

namespace HeimrichHannot\FormTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;

/**
 * @Callback(table="tl_form", target="fields.formType.options")
 */
class FormTypeFieldOptionsCallback
{
    public function __construct(private readonly FormTypeCollection $formTypeCollection)
    {
    }

    public function __invoke(): array
    {
        $options = [];

        foreach ($this->formTypeCollection->getTypes() as $type) {
            $options[$type->getType()] = $type->getType();
        }

        return $options;
    }

}