<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;

/**
 * @Callback(table="tl_form", target="fields.formgeneratorType.options")
 */
class FormGeneratorTypeFieldOptionsCallback
{
    private FormgeneratorTypeCollection $formgeneratorTypeCollection;

    public function __construct(FormgeneratorTypeCollection $formgeneratorTypeCollection)
    {
        $this->formgeneratorTypeCollection = $formgeneratorTypeCollection;
    }

    public function __invoke(): array
    {
        $options = [];

        foreach ($this->formgeneratorTypeCollection->getTypes() as $type) {
            $options[$type->getType()] = $type->getType();
        }

        return $options;
    }

}