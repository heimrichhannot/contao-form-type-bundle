# Form Type Bundle

Form type bundle is an extension for the Contao CMS to provide a generic way to create forms for specific approaches with contao form generator. 
It is aimed at developers and contains no build-in form types.

## Feature
* Generic way to create forms for specific approaches with contao form generator
* Options event for select, checkbox and radio form fields
* Form types can support a first time wizard to setup basic form fields

## Installation

Install the bundle via composer and update your database afterwards:

```
composer require heimrichhannot/contao-form-type-bundle
```

## Usage

### Create a new form type

Create a new class that extends `AbstractFormType`. Register it as service with autoconfiguration enabled.

A label can be set within `$GLOBALS['TL_LANG']['tl_form']['FORMTYPE']`:

```php
# contao/languages/de/tl_form.php
$GLOBALS['TL_LANG']['tl_form']['FORMTYPE']['huh_mediathek'] = 'Mediathek';
```

### First time wizard

You can add a first time wizard to your form type by implementing `getDefaultFields()` method.
It expects field definitions in array format as return values. 
These fields will be created when executing the wizard.

```php
public function getDefaultFields(FormModel $formModel): array
{
    return [
        [
            'type' => 'text',
            'name' => 'title',
            'label' => $this->translator->trans('tl_example.title.0', [], 'contao_tl_example'),
            'mandatory' => '1',
        ],
        [
            'type' => 'textarea',
            'name' => 'text',
            'label' => $this->translator->trans('tl_example.text.0', [], 'contao_tl_example'),
        ],
    ];
}
```

### Form events

You can register event listeners for events during form livecycle.
These events are mappings of the corresponding contao form hooks but will only fire for the specific form type.

Following events are available:

| Event                  | Name                                     |
|------------------------|------------------------------------------|
| CompileFormFieldsEvent | huh.form_type.[TYPE].compile_form_fields |
| LoadFormFieldEvent     | huh.form_type.[TYPE].load_form_field     |
| PrepareFormDataEvent   | huh.form_type.[TYPE].prepare_form_data   |
| ProcessFormDataEvent   | huh.form_type.[TYPE].process_form_data   |
| StoreFormDataEvent     | huh.form_type.[TYPE].store_form_data     |
| ValidateFormFieldEvent | huh.form_type.[TYPE].validate_form_field |


### Options event

If you want to provide options for a select, checkbox or radio form field, you can register an event listener. 
The event name is `huh.form_type.<formtype>.<field>.options'`.

```php
// src/EventListener/OptionsEventListener.php
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;

class OptionsEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'huh.form_type.huh_media_library.licence.options' => 'onLicenceOptions',
        ];
    }

    public function onLicenceOptions(FieldOptionsEvent $event): void
    {
        $event->addOption('free', 'Free');
        $event->addOption('adobe', 'Adobe');
        $event->addOption('istock', 'iStock');
    }
    
    public function onTopicOptions(FieldOptionsEvent $event): void
    {
        $event->addOption('cinema', 'Cinema', 'entertainment');
        $event->addOption('music', 'Music', 'entertainment');
        
        $event->addOption('politics', 'Politics', 'news');
        $event->addOption('sport', 'Sport', 'news');
        $event->addOption('culture', 'Culture', 'news');
    }
}
```

#### Unified Dispatcher for Field Options

Use `FieldOptionsDispatcherTrait` to dispatch `FieldOptionsEvent` `huh.form_type.<formtype>.<field>.options` and Contao `fields.<field>.options` callbacks alike.


Example:
```php
use HeimrichHannot\MediaLibraryBundle\Trait\FieldOptionsDispatcherTrait;

class MyContainerOrFormType
{
    use FieldOptionsDispatcherTrait;

    #[AsCallback(table: 'tl_ml_product', target: 'fields.licence.options')]
    #[AsEventListener('huh.form_type.huh_media_library.licence.options')]
    public function getLicenceOptions(): array
    {
        return $this->dispatchFieldOptions([
            'free' => 'Released for use under indication of copyright',
            'locked' => 'Subject to licence'
        ]);
    }
}
```

### Get form options

Use the `FormTypeCollection` class to get form options to use in your dca fields or wherever you need them.

```php
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use HeimrichHannot\EventRegistrationBundle\FormType\EventRegistrationFormType;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;

#[AsCallback(table: 'tl_calendar', target: 'fields.reg_form.options')]
class FieldsRegFormOptionsListener
{
    private readonly FormTypeCollection $formTypeCollection,

    public function __invoke(): array
    {
        return $this->formTypeCollection->getFormsForFormType(EventRegistrationFormType::TYPE);
    }
}
```

### Form Context

Implementing a form context evaluator on your from type allows you to change the form's behavior depending on the context it is used in.
This can be used e.g. to load existing data into the form fields, perhaps to create an editing form.

#### Built-in Form Edit Context

A basic default form context evaluator providing editing functionality is built into the bundle.
Just override `DEFAULT_FORM_CONTEXT_TABLE` in your form type to allow editing the respective database model:
```php
protected const DEFAULT_FORM_CONTEXT_TABLE = 'tl_my_table';
```

* Create your form in Contao.
* Name your form fields like the database fields you want to edit, e.g. `title` for `tl_my_table.title`.
* Append the query parameter `?edit=<id>` to the url of the page your form is included in to edit the existing row with a primary key of `<id>`.

If no `DEFAULT_FORM_CONTEXT_TABLE` is set, the form will be treated as a creation form by default.

#### Create your own Form Context

To implement your own form context evaluator, override `evaluateFormContext()` in your form type:
```php
protected function evaluateFormContext(): FormContext
{
    $request = $this->container->get('request_stack')->getCurrentRequest();
    $editParameter = 'edit';
    $databaseTable = 'tl_my_table';

    if ($modelPk = $request->query->get($editParameter))
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = Model::getClassFromTable($databaseTable);
        $modelInstance = $modelClass::findByPk($modelPk);
        if ($modelInstance === null) {
            return FormContext::invalid($databaseTable, 'Could not find object.');
        }
        return FormContext::update($databaseTable, $modelInstance->row());
    }

    return FormContext::create($databaseTable);
}
```

The example shows the implementation of a form context evaluator that allows editing a database model with a primary key given by the query parameter `edit`.

To retrieve the current form context, call the getter on your form type:
```php
$formContext = $this->getFormContext();
```

We do not recommend to call `$this->evaluateFormContext()` directly, as it will skip future cache implementations if you do.

#### Form Context Actions

Most commonly used form context actions are already implemented.

```php
use HeimrichHannot\FormTypeBundle\FormType\FormContextAction;

FormContextAction::CREATE;
FormContextAction::READ;
FormContextAction::UPDATE;
FormContextAction::DELETE;
FormContextAction::CLONE;
FormContextAction::INVALID;
```

`FormContextAction::INVALID` can be used to indicate that the form should not be processed.

To instantiate a `FormContext` object with a built-in action, use the static factory methods:

```php
use HeimrichHannot\FormTypeBundle\FormType\FormContext;

$createContext  = FormContext::create('tl_my_table');
$readContext    = FormContext::read('tl_my_table', $data);
$updateContext  = FormContext::update('tl_my_table', $data);
$deleteContext  = FormContext::delete('tl_my_table', $data);

$cloneContext   = FormContext::clone('tl_my_table', $data);

$invalidContext = FormContext::invalid('tl_my_table', 'This is error detail.', $additionalData ?? []);
```

Alternatively, you may also use the `FormContext` constructor:

```php
use HeimrichHannot\FormTypeBundle\FormType\FormContext;
use HeimrichHannot\FormTypeBundle\FormType\FormContextAction;

$formContext = new FormContext(FormContextAction::UPDATE, 'tl_my_table', $data);
```


However, you may also specify your own actions when constructing a `FormContext` object, or by overriding the action of an existing object.

```php
use HeimrichHannot\FormTypeBundle\FormType\FormContext;
use HeimrichHannot\FormTypeBundle\FormType\FormContextAction;

$formContext = new FormContext('my_custom_action', 'tl_my_table', $data);
// or
$formContext->setAction('this_can_be_any_string_or_action');
// or
$formContext->setAction(FormContextAction::DELETE);
```

This way you are not bound to the built-in actions.
