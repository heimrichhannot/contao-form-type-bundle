# Changelog

All notable changes to this project will be documented in this file.

## [0.2.5] - 2025-07-10
- Changed: make label editable in prepareFormData event
- Changed: deprecated getter and setter methods in prepareFormData event

## [0.2.4] - 2025-06-18
- Changed: make label data editable in processFormData event

## [0.2.3] - 2025-06-17
- Added: FormTypeCollection::getFormsForFormType() to retrieve forms for a specific form type

## [0.2.2] - 2025-06-02
- Fixed: undefined variable exception

## [0.2.1] - 2025-06-02
- Added: pass last insertId to processFormData event
- Changed: make submitted data editable in processFormData event
- Changed: make event properties public in processFormData event

## [0.2.0] - 2024-08-19
- Added: Require form to evaluate form context
- Added: Form context caching
- Changed: Now using attributes instead of annotations
- Changed: Minor refactoring for code quality and legibility
- Removed: Support for Contao < 4.13

## [0.1.11] - 2024-07-19
- Fixed: deprecated VERSION constant

## [0.1.10] - 2024-07-11
- Fixed: compile error on missing type

## [0.1.9] - 2024-04-30
- Changed: require at least php 8.1
- Changed: LoadFormFieldEvent now containers the form context
- Deprecated: LoadFormFieldEvent::setWidget()

## [0.1.8] - 2024-02-02
- Added: form events

## [0.1.7] - 2023-12-11
- Added: Forms can now be edited
- Added: Custom form context support

## [0.1.5] - 2023-11-07
- Added: getForm hook

## [0.1.4] - 2023-11-07
- Added: hooks: onLoadFormField, onCompileFormFields

## [0.1.3] - 2023-11-06
- Added: onValidateFormField hook

## [0.1.2] - 2023-10-10
- Fixed: missing method in AbstractFormType

## [0.1.1] - 2023-09-05
- Added: FieldOptionsEvent setOptionsByKeyValue method

## [0.1.0] - 2023-09-05
Initial version.
