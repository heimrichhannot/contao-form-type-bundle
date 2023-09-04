<?php

namespace HeimrichHannot\FormTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Database;
use Contao\Form;
use Contao\Widget;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormGeneratorListener
{
    private FormTypeCollection $formTypeCollection;
    private array $files = [];
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(FormTypeCollection $formTypeCollection, EventDispatcherInterface $eventDispatcher)
    {
        $this->formTypeCollection = $formTypeCollection;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Hook("loadFormField", priority=17)
     */
    public function onLoadFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($form->formType && $formType = $this->formTypeCollection->getType($form->formType)) {
            if (in_array($widget->type, ['select', 'radio', 'checkbox'])) {
                /** @var FieldOptionsEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FieldOptionsEvent($widget, $form, $widget->options),
                    'huh.form_type.'.$formType->getType().'.'.str_replace('[]', '', $widget->name).'.options'
            );
                if ($event->isDirty()) {
                    $widget->options = $event->getOptions();
                }
            }
        }

        return $widget;
    }

    /**
     * @Hook("prepareFormData", priority=17)
     */
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form, array $files = []): void
    {
        if ($form->formType && $formType = $this->formTypeCollection->getType($form->formType)) {
            if (version_compare('5.0', VERSION.'.'.BUILD)) {
                $this->files[$form->formID] = $files;
            } else {
                $this->files[$form->formID] = $_SESSION['FILES'] ?? [];
            }
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
            $event = new StoreFormDataEvent($data, $form, $this->files[$form->formID] ?? []);
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