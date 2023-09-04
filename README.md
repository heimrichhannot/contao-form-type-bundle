# Form Type Bundle

Form type bundle is an extension for the Contao CMS to provide a generic way to create forms for specific approaches with contao form generator. 
It is aimed at developers and contains no build-in form types.

## Installation

Install the bundle via composer and update your database afterwards:

```
composer require heimrichhannot/contao-form-type-bundle
```

## Usage

### Create a new form type

Create a new class that implements the `FormTypeInterface` and register it as service with autoconfiguration enabled.


