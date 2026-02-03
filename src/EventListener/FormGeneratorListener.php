<?php

namespace HeimrichHannot\FormTypeBundle\EventListener;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Date;
use Contao\Form;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Input;
use Contao\StringUtil;
use Contao\Widget;
use DateTime;
use Doctrine\DBAL\Connection;
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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FormGeneratorListener
{
    private array $files = [];

    public function __construct(
        private readonly FormTypeCollection       $formTypeCollection,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Connection              $connection
    ) {}

    #[AsHook("loadFormField", priority: 17)]
    public function onLoadFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        $formType = $this->formTypeCollection->getType($form);
        if (!$formType) {
            return $widget;
        }

        if (\in_array($widget->type, ['select', 'radio', 'checkbox'])) {
            $this->loadChoiceWidgetCallback($widget, $form, $formType);
        }

        $formContext = $formType->getFormContext($form);
        $data = $formContext->getData();

        if (!empty($widget->name) && !$formContext->isCreate()) {
            $value = $data[str_replace('[]', '', $widget->name)] ?? null;
            $value = StringUtil::deserialize($value) ?? $value ?? $widget->value;
            $widget->value = $value;
        }

        if (Input::post('FORM_SUBMIT') !== $formId
            && !$formContext->isCreate()
            && \in_array($widget->rgxp, ['date', 'time', 'datim']))
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

        $fieldOptionsEvent = new LoadFormFieldEvent($widget, $formId, $formData, $form, $formContext);
        $formType->onLoadFormField($fieldOptionsEvent);
        $this->eventDispatcher->dispatch($fieldOptionsEvent, "huh.form_type.{$formType->getType()}.load_form_field");

        return $fieldOptionsEvent->getWidget();
    }

    private function loadChoiceWidgetCallback(
        Widget           $widget,
        Form             $form,
        AbstractFormType $formType
    ): void {
        if (!\is_array($arrOptions = $widget->options) || empty($arrOptions))
        {
            $arrOptions = [];
        }

        /** @var FieldOptionsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new FieldOptionsEvent($widget, $form, $arrOptions),
            \sprintf(
                'huh.form_type.%s.%s.options',
                $formType->getType(),
                \str_replace('[]', '', $widget->name)
            )
        );

        if ($event->isDirty())
        {
            $options = $event->getOptions();

            if ($event->isEmptyOption())
            {
                $options = \array_merge([
                    $event->createOptions('', $event->getEmptyOptionLabel()),
                ], $options);
            }

            $options = $event->isGrouped()
                ? $this->formatGroupedOptions($options, $event->isSorted())
                : $this->formatUngroupedOptions($options, $event->isSorted());

            $widget->options = $options;
        }
    }

    private function formatGroupedOptions(array $options, bool $sorted): array
    {
        \usort($options, static function ($a, $b) use ($sorted): int {
            $comp = ($a['group'] ?? '') <=> ($b['group'] ?? '');
            if ($comp || !$sorted) {
                return $comp;
            }
            $aa = \mb_strtolower($a['label'] ?? $a['value'] ?? '');
            $bb = \mb_strtolower($b['label'] ?? $b['value'] ?? '');
            return $aa <=> $bb;
        });

        $return = [];
        $previousGroup = null;

        foreach ($options as $setting)
        {
            $group = ($setting['group'] ?? null) ?: null;
            unset($setting['group']);

            if ($group !== $previousGroup)
            {
                $groupAlias = \preg_replace('/[^a-z0-9]/i', '_', \strtolower($group));
                $groupAlias = \preg_replace('/_+/', '_', $groupAlias);
                $groupAlias = \substr($groupAlias, 0, 10);

                $previousGroup = $group;
                $return[] = [
                    'group' => $group,
                    'label' => $group,
                    'value' => "__group__{$groupAlias}__"
                ];
            }

            $return[] = $setting;
        }

        return $return;
    }

    private function formatUngroupedOptions(array $options, bool $sorted): array
    {
        \array_walk($options, static function (array &$option) {
            unset($option['group']);
        });

        if ($sorted) {
            \usort($options, static function (array $a, array $b) {
                $aa = \mb_strtolower($a['label'] ?? $a['value'] ?? '');
                $bb = \mb_strtolower($b['label'] ?? $b['value'] ?? '');
                return $aa <=> $bb;
            });
        }

        return $options;
    }

    #[AsHook("prepareFormData", priority: 17)]
    public function onPrepareFormData(array &$submittedData, array &$labels, array $fields, Form $form, array $files = []): void
    {
        $formType = $this->formTypeCollection->getType($form);
        if (!$formType) {
            return;
        }

        if (\version_compare(ContaoCoreBundle::getVersion(), '5.0', '>=')) {
            // Code for Contao 5.0 or later
            $this->files[$form->formID] = $files;
        } else {
            // Code for Contao versions earlier than 5.0
            $this->files[$form->formID] = $_SESSION['FILES'] ?? [];
        }

        foreach (FormFieldModel::findByPid($form->id) as $formField)
        {
            $data = $submittedData[$formField->name] ?? null;
            if ($data === null) {
                continue;
            }

            if (\in_array($formField->rgxp, ['date', 'time', 'datim'])) {
                $objDate = new Date($data, Date::getFormatFromRgxp($formField->rgxp));
                $submittedData[$formField->name] = $objDate->tstamp;
            }

            if (\is_array($data)) {
                $submittedData[$formField->name] = serialize($data);
            }
        }

        $event = new PrepareFormDataEvent($submittedData, $labels, $fields, $form);
        $formType->onPrepareFormData($event);
        $this->eventDispatcher->dispatch($event, "huh.form_type.{$formType->getType()}.prepare_form_data");

        $submittedData = $event->data;
        $labels = $event->labels;
    }

    #[AsHook("storeFormData", priority: 17)]
    public function onStoreFormData(array $data, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new StoreFormDataEvent($data, $form, $this->files[$form->formID] ?? []);
            $formType->onStoreFormData($event);
            $this->eventDispatcher->dispatch($event, "huh.form_type.{$formType->getType()}.store_form_data");
            $data = $event->getData();
        }
        return $data;
    }

    #[AsHook("processFormData", priority: 17)]
    public function onProcessFormData(array &$submittedData, array $formData, ?array $files, array &$labels, Form $form): void
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $insertId = null;
            if ($form->storeValues && $form->targetTable) {
                $insertId = $this->connection->lastInsertId();
            }

            $event = new ProcessFormDataEvent($submittedData, $formData, $files, $labels, $form, $insertId);
            $this->eventDispatcher->dispatch($event, "huh.form_type.{$formType->getType()}.process_form_data");
            $formType->onProcessFormData($event);

            $submittedData = $event->submittedData;
            $labels = $event->labels;
        }
    }

    #[AsHook("validateFormField", priority: 17)]
    public function onValidateFormField(Widget $widget, string $formId, array $formData, Form $form): Widget
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new ValidateFormFieldEvent($widget, $formId, $formData, $form);
            $formType->onValidateFormField($event);
            $this->eventDispatcher->dispatch($event, "huh.form_type.{$formType->getType()}.validate_form_field");
            $widget = $event->getWidget();
        }
        return $widget;
    }

    #[AsHook("compileFormFields", priority: 17)]
    public function onCompileFormFields(array $fields, string $formId, Form $form): array
    {
        if ($formType = $this->formTypeCollection->getType($form))
        {
            $event = new CompileFormFieldsEvent($fields, $formId, $form);
            $formType->onCompileFormFields($event);
            $this->eventDispatcher->dispatch($event, "huh.form_type.{$formType->getType()}.compile_form_fields");
            $fields = $event->getFields();
        }
        return $fields;
    }

    #[AsHook("getForm", priority: 17)]
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