<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\Form;
use HeimrichHannot\FormgeneratorTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;
use HeimrichHannot\MediaLibraryBundle\Model\ProductModel;

class FormGeneratorListener
{
    private FormgeneratorTypeCollection $formgeneratorTypeCollection;

    public function __construct(FormgeneratorTypeCollection $formgeneratorTypeCollection)
    {
        $this->formgeneratorTypeCollection = $formgeneratorTypeCollection;
    }

    /**
     * @Hook("prepareFormData")
     */
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if ($form->formgeneratorType && $formType = $this->formgeneratorTypeCollection->getType($form->formgeneratorType)) {
            $event = new PrepareFormDataEvent($submittedData, $labels, $fields, $form);
            $formType->onPrepareFormData($event);
            $submittedData = $event->getData();
        }
    }

    /**
     * @Hook("storeFormData")
     */
    public function onStoreFormData(array $data, Form $form): array
    {
        if ($form->formgeneratorType && $formType = $this->formgeneratorTypeCollection->getType($form->formgeneratorType)) {
            $event = new StoreFormDataEvent($data, $form);
            $formType->onStoreFormData($event);
            $data = $event->getData();
        }
        return $data;
    }
}