<?php

namespace HeimrichHannot\FormTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Form;
use Contao\FormModel;
use Contao\Widget;
use HeimrichHannot\FormTypeBundle\Event\AbstractFormEvent;
use HeimrichHannot\FormTypeBundle\Event\CompileFormFieldsEvent;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\GetFormEvent;
use HeimrichHannot\FormTypeBundle\Event\LoadFormFieldEvent;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ValidateFormFieldEvent;
use HeimrichHannot\FormTypeBundle\FormType\AbstractFormType;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormGeneratorListener
{
    private array $files = [];

    public function __construct(
        private readonly FormTypeCollection       $formTypeCollection,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack             $requestStack
    )
    {
    }

    private function evaluateFormContext(AbstractFormType $formType, AbstractFormEvent $event): void
    {
        $contextConfig = $formType->getFormContextConfig();

        if ($contextConfig !== null)
        {
            $event->setFormContext(
                $contextConfig->evaluate($this->requestStack->getCurrentRequest())
            );
        }
    }

    /**
     * @Hook("loadFormField", priority=17)
     */
    public function onLoadFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            if (in_array($widget->type, ['select', 'radio', 'checkbox'])) {
                /** @var FieldOptionsEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FieldOptionsEvent($widget, $form, $widget->options),
                    'huh.form_type.'.$formType->getType().'.'.str_replace('[]', '', $widget->name).'.options'
                );
                if ($event->isDirty()) {
                    $options = $event->getOptions();
                    if ($event->isEmptyOption()) {
                        $options = array_merge([$event->createOptions('', $event->getEmptyOptionLabel())], $options);
                    }
                    $widget->options = $options;
                }
            }

            $event = new LoadFormFieldEvent($widget, $formId, $formData, $form);
            $formType->onLoadFormField($event);
            $widget = $event->getWidget();
        }

        return $widget;
    }

    /**
     * @Hook("prepareFormData", priority=17)
     */
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form, array $files = []): void
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            if (version_compare('5.0', VERSION.'.'.BUILD)) {
                $this->files[$form->formID] = $files;
            } else {
                $this->files[$form->formID] = $_SESSION['FILES'] ?? [];
            }
            $event = new PrepareFormDataEvent($submittedData, $labels, $fields, $form);
            $this->evaluateFormContext($formType, $event);
            $formType->onPrepareFormData($event);
            $submittedData = $event->getData();
        }
    }

    /**
     * @Hook("storeFormData", priority=17)
     */
    public function onStoreFormData(array $data, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            $event = new StoreFormDataEvent($data, $form, $this->files[$form->formID] ?? []);
            $this->evaluateFormContext($formType, $event);
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
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            $event = new ProcessFormDataEvent($submittedData, $formData, $files, $labels, $form);
            $this->evaluateFormContext($formType, $event);
            $formType->onProcessFormData($event);
        }
    }

    /**
     * @Hook("validateFormField", priority=17)
     */
    public function onValidateFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            $event = new ValidateFormFieldEvent($widget, $formId, $formData, $form);
            $formType->onValidateFormField($event);
            $widget = $event->getWidget();
        }
        return $widget;
    }

    /**
     * @Hook("compileFormFields", priority=17)
     */
    public function onCompileFormFields(array $fields, string $formId, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            $event = new CompileFormFieldsEvent($fields, $formId, $form);
            $formType->onCompileFormFields($event);
            $fields = $event->getFields();
        }
        return $fields;
    }

    /**
     * @Hook("getForm", priority=17)
     */
    public function onGetForm(FormModel $formModel, string $buffer, Form $form): string
    {
        if ($formType = $this->formTypeCollection->getTypeOfForm($form))
        {
            $event = new GetFormEvent($formModel, $buffer, $form);
            $this->evaluateFormContext($formType, $event);
            $formType->onGetForm($event);
            $buffer = $event->getBuffer();
        }
        return $buffer;
    }

}