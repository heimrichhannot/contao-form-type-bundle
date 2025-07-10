<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\DataContainer;
use Contao\Form;
use Contao\FormModel;
use Contao\Model;
use HeimrichHannot\FormTypeBundle\Event\CompileFormFieldsEvent;
use HeimrichHannot\FormTypeBundle\Event\GetFormEvent;
use HeimrichHannot\FormTypeBundle\Event\LoadFormFieldEvent;
use HeimrichHannot\FormTypeBundle\Event\PrepareFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ProcessFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\FormTypeBundle\Event\ValidateFormFieldEvent;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class AbstractFormType implements FormTypeInterface, ServiceSubscriberInterface
{
    protected const CACHE_FORM_CONTEXT = true;
    protected const DEFAULT_FORM_CONTEXT_TABLE = null;

    protected ?ContainerInterface $container = null;
    private array $contextCache = [];

    /**
     * @required
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'database_connection' => '?database_connection',
            'request_stack' => '?request_stack',
        ];
    }

    public function getType(): string
    {
        $className = \ltrim(\strrchr(static::class, '\\'), '\\');

        if (\str_ends_with($className, 'FormType'))
        {
            $className = \substr($className, 0, -8);
        }
        elseif (\str_ends_with($className, 'Type'))
        {
            $className = \substr($className, 0, -4);
        }

        return Container::underscore($className);
    }

    public function getDefaultFields(FormModel $formModel): array
    {
        return [];
    }

    final public function invalidateCache(): void
    {
        $this->contextCache = [];
    }

    final public function getFormContext(Form $form, bool $cached = null): FormContext
    {
        if (!($cached ?? static::CACHE_FORM_CONTEXT)) {
            return $this->evaluateFormContext($form);
        }

        $formId = $form->id;

        if (!isset($this->contextCache[$formId])) {
            $this->contextCache[$formId] = $this->evaluateFormContext($form);
        }

        return $this->contextCache[$formId];
    }

    protected function evaluateFormContext(Form $form): FormContext
    {
        if (!static::DEFAULT_FORM_CONTEXT_TABLE) {
            return FormContext::create();
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $editParameter = 'edit';

        if ($modelPk = $request->query->get($editParameter))
        {
            /** @var class-string<Model> $modelClass */
            $modelClass = Model::getClassFromTable(static::DEFAULT_FORM_CONTEXT_TABLE);
            $modelInstance = $modelClass::findByPk($modelPk);
            if ($modelInstance === null) {
                return FormContext::invalid(static::DEFAULT_FORM_CONTEXT_TABLE, 'Could not find object.');
            }
            return FormContext::update(static::DEFAULT_FORM_CONTEXT_TABLE, $modelInstance->row());
        }

        return FormContext::create(static::DEFAULT_FORM_CONTEXT_TABLE);
    }

    abstract public function onload(DataContainer $dataContainer, FormModel $formModel): void;

    public function onPrepareFormData(PrepareFormDataEvent $event): void
    {
        $form = $event->form;
        $formContext = $this->getFormContext($form);

        if ($formContext->isInvalid())
        {
            $errorClass = $formContext->getData()['_errorClass'] ?? BadRequestHttpException::class;
            throw new $errorClass($formContext->getData()['_detail'] ?? 'Invalid form context.');
        }

        if ($formContext->isRead() || $formContext->isUpdate() || $formContext->isDelete())
        {
            $event->form->storeValues = '';
        }
    }

    public function onStoreFormData(StoreFormDataEvent $event): void
    {
    }

    public function onProcessFormData(ProcessFormDataEvent $event): void
    {
        if ($this->getFormContext($event->getForm())->isUpdate())
        {
            $this->onUpdate($event);
        }
    }

    public function onValidateFormField(ValidateFormFieldEvent $event): void
    {
    }

    public function onCompileFormFields(CompileFormFieldsEvent $event): void
    {
    }

    public function onLoadFormField(LoadFormFieldEvent $event): void
    {
    }

    public function onGetForm(GetFormEvent $event): void
    {
    }

    protected function onUpdate(ProcessFormDataEvent $event): void
    {
        $form = $event->getForm();
        $formContext = $this->getFormContext($form);
        $oldData = $formContext->getData();
        $newData = $event->getSubmittedData();
        $validKeys = array_keys($oldData);

        $setData = [];

        foreach ($newData as $key => $newValue)
        {
            if (in_array($key, ['dateAdded', 'alias'])
                || !in_array($key, $validKeys))
            {
                continue;
            }

            $oldValue = $oldData[$key] ?? null;

            if ($newValue !== $oldValue
                && !(empty($newValue) && empty($oldValue)))
            {
                $setData[$key] = $newValue ?? null;
            }
        }

        if (sizeof($setData) < 1) {
            return;
        }

        if (in_array('tstamp', $validKeys)) {
            $setData['tstamp'] = time();
        }

        $sql = "UPDATE %s SET %s WHERE id = ?";

        $sql = sprintf(
            $sql,
            $formContext->getTable(),
            implode(', ', array_map(fn ($key) => $key . ' = ?', array_keys($setData)))
        );

        $stmt = $this->container->get('database_connection')->prepare($sql);
        $stmt->executeStatement([...array_values($setData), $formContext->getData()['id']]);
    }
}