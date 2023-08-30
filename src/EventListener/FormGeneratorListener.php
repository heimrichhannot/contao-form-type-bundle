<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\Form;
use HeimrichHannot\FormgeneratorTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;

class FormGeneratorListener
{
    private FormgeneratorTypeCollection $formgeneratorTypeCollection;

    public function __construct(FormgeneratorTypeCollection $formgeneratorTypeCollection)
    {
        $this->formgeneratorTypeCollection = $formgeneratorTypeCollection;
    }

    /**
     * @Hook("prepareFormData", priority=17)
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
     * @Hook("storeFormData", priority=17)
     */
    public function onStoreFormData(array $data, Form $form): array
    {
        if ($form->formgeneratorType && $formType = $this->formgeneratorTypeCollection->getType($form->formgeneratorType)) {
            $event = new StoreFormDataEvent($data, $form, $_SESSION['FILES'] ?? []);
            $formType->onStoreFormData($event);
            $data = $event->getData();
        }
        return $data;
    }

    /**
     * @Hook("processFormData", priority=17)
     */
    public function onProcessFormData(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
        if ($form->formgeneratorType && $formType = $this->formgeneratorTypeCollection->getType($form->formgeneratorType)) {
            $event = new ProcessFormDataEvent($submittedData, $formData, $files, $labels, $form);
            $formType->onProcessFormData($event);
        }
    }
}