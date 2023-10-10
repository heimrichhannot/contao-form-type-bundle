# Form Type Bundle

Form type bundle is an extension for the Contao CMS to provide a generic way to create forms for specific approaches with contao form generator. 
It is aimed at developers and contains no build-in form types.

## Feature
* generic way to create forms for specific approaches with contao form generator
* options event for select, checkbox and radio form fields
* form types can support a first time wizard to setup basic form fields

## Installation

Install the bundle via composer and update your database afterwards:

```
composer require heimrichhannot/contao-form-type-bundle
```

## Usage

### Create a new form type

Create a new class that extends `[AbstractFormType.php](src%2FFormType%2FAbstractFormType.php)` (recommended) or implements the `FormTypeInterface`. Register it as service with autoconfiguration enabled.

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
}
```


