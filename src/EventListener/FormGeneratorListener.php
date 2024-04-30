<?php

namespace HeimrichHannot\FormTypeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Date;
use Contao\Form;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\StringUtil;
use Contao\Widget;
use DateTime;
use HeimrichHannot\FormTypeBundle\Event\CompileFormFieldsEvent;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\GetFormEvent;
use HeimrichHannot\FormTypeBundle\Event\LoadFormFieldEvent;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ValidateFormFieldEvent;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormGeneratorListener
{
    private array $files = [];
    private readonly EventDispatcherInterface $eventDispatcher;

    public function __construct(
        private readonly FormTypeCollection       $formTypeCollection,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Hook("loadFormField", priority=17)
     */
    public function onLoadFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            if (in_array($widget->type, ['select', 'radio', 'checkbox'])) {
                /** @var FieldOptionsEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FieldOptionsEvent($widget, $form, $widget->options),
                    'huh.form_type.'.$formType->getType().'.'.str_replace('[]', '', (string) $widget->name).'.options'
                );
                if ($event->isDirty()) {
                    $options = $event->getOptions();
                    if ($event->isEmptyOption()) {
                        $options = array_merge([$event->createOptions('', $event->getEmptyOptionLabel())], $options);
                    }
                    $widget->options = $options;
                }
            }

            $formContext = $formType->getFormContext();
            $data = $formContext->getData();

            if (!empty($widget->name) && !$formContext->isCreate()) {
                $value = $data[str_replace('[]', '', (string) $widget->name)] ?? null;
                $value = StringUtil::deserialize($value) ?? $value ?? $widget->value;
                $widget->value = $value;
            }

            if (Input::post('FORM_SUBMIT') !== $formId
                && !$formContext->isCreate()
                && in_array($widget->rgxp, ['date', 'time', 'datim']))
            {
                try {
                    $date = Date::parse('Y-m-d H:i:s', $widget->value);
                    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date);
                    $widget->value = $dt ? match ($widget->rgxp) {
                        'date' => $dt->format(Date::getNumericDateFormat()),
                        'time' => $dt->format(Date::getNumericTimeFormat()),
                        'datim' => $dt->format(Date::getNumericDatimFormat()),
                        default => $widget->value
                    } : null;
                } catch (\Exception) {
                    $widget->value = null;
                }
            }

            $event = new LoadFormFieldEvent($widget, $formId, $formData, $form, $formContext);
            $formType->onLoadFormField($event);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.load_form_field');
            $widget = $event->getWidget();
        }

        return $widget;
    }

    /**
     * @Hook("prepareFormData", priority=17)
     */
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form, array $files = []): void
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            if (version_compare('5.0', VERSION.'.'.BUILD)) {
                $this->files[$form->formID] = $files;
            } else {
                $this->files[$form->formID] = $_SESSION['FILES'] ?? [];
            }

            foreach (FormFieldModel::findByPid($form->id) as $formField)
            {
                $data = $submittedData[$formField->name] ?? null;

                if ($data === null) {
                    continue;
                }

                if (in_array($formField->rgxp, ['date', 'time', 'datim'])) {
                    $objDate = new Date($data, Date::getFormatFromRgxp($formField->rgxp));
                    $submittedData[$formField->name] = $objDate->tstamp;
                }

                if (is_array($data)) {
                    $submittedData[$formField->name] = serialize($data);
                }
            }

            $event = new PrepareFormDataEvent($submittedData, $labels, $fields, $form);
            $formType->onPrepareFormData($event);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.prepare_form_data');
            $submittedData = $event->getData();
        }
    }

    /**
     * @Hook("storeFormData", priority=17)
     */
    public function onStoreFormData(array $data, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new StoreFormDataEvent($data, $form, $this->files[$form->formID] ?? []);
            $formType->onStoreFormData($event);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.store_form_data');
            $data = $event->getData();
        }
        return $data;
    }

    /**
     * @Hook("processFormData", priority=17)
     */
    public function onProcessFormData(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new ProcessFormDataEvent($submittedData, $formData, $files, $labels, $form);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.process_form_data');
            $formType->onProcessFormData($event);
        }
    }

    /**
     * @Hook("validateFormField", priority=17)
     */
    public function onValidateFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new ValidateFormFieldEvent($widget, $formId, $formData, $form);
            $formType->onValidateFormField($event);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.validate_form_field');
            $widget = $event->getWidget();
        }
        return $widget;
    }

    /**
     * @Hook("compileFormFields", priority=17)
     */
    public function onCompileFormFields(array $fields, string $formId, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new CompileFormFieldsEvent($fields, $formId, $form);
            $formType->onCompileFormFields($event);
            $this->eventDispatcher->dispatch($event, 'huh.form_type.'.$formType->getType().'.compile_form_fields');
            $fields = $event->getFields();
        }
        return $fields;
    }

    /**
     * @Hook("getForm", priority=17)
     */
    public function onGetForm(FormModel $formModel, string $buffer, Form $form): string
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new GetFormEvent($formModel, $buffer, $form);
            $formType->onGetForm($event);
            $buffer = $event->getBuffer();
        }
        return $buffer;
    }

}