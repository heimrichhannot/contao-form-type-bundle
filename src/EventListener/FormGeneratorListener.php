<?php

namespace HeimrichHannot\FormTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\Form;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;

class FormGeneratorListener
{
    private FormTypeCollection $formTypeCollection;

    public function __construct(FormTypeCollection $formTypeCollection)
    {
        $this->formTypeCollection = $formTypeCollection;
    }

    /**
     * @Hook("prepareFormData", priority=17)
     */
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if ($form->formType && $formType = $this->formTypeCollection->getType($form->formType)) {
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
        if ($form->formType && $formType = $this->formTypeCollection->getType($form->formType)) {
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
        if ($form->formType && $formType = $this->formTypeCollection->getType($form->formType)) {
            $event = new ProcessFormDataEvent($submittedData, $formData, $files, $labels, $form);
            $formType->onProcessFormData($event);
        }
    }
}