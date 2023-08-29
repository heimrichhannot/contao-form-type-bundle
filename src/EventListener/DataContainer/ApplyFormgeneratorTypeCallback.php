<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\FormModel;
use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;

/**
 * @Callback(table="tl_form", target="config.onload")
 */
class ApplyFormgeneratorTypeCallback
{
    private FormgeneratorTypeCollection $formgeneratorTypeCollection;

    public function __construct(FormgeneratorTypeCollection $formgeneratorTypeCollection)
    {
        $this->formgeneratorTypeCollection = $formgeneratorTypeCollection;
    }

    public function __invoke(DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $formModel = FormModel::findByPk($dc->id);
        if (!$formModel->formgeneratorType) {
            return;
        }

        $type = $this->formgeneratorTypeCollection->getType($formModel->formgeneratorType);
        if (!$type) {
            return;
        }

        $type->onload($dc, $formModel);
    }

}