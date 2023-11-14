<?php

namespace HeimrichHannot\FormTypeBundle\Trait;

use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;

/**
 * One trait to rule them all.
 * Add this trait to your data container class and use it to dispatch field options events, both for Contao callbacks and Symfony event listeners.
 */
trait FieldOptionsDispatcherTrait
{
    /**
     * Dispatches field options events for Contao callbacks and Symfony event listeners.
     *
     * Example:
     * ```php
     * #[AsCallback(table: 'tl_ml_product', target: 'fields.licence.options')]
     * #[AsEventListener('huh.form_type.huh_media_library.licence.options')]
     * public function getLicenceOptions(): array
     * {
     *     return $this->dispatchFieldOptions([
     *         'free' => 'Released for use under indication of copyright',
     *         'locked' => 'Subject to license'
     *     ]);
     * }
     * ```
     *
     * @param array $options The options array to dispatch.
     * @param array|null $funcArgs The function arguments to check for FieldOptionsEvent instances. Defaults to the calling function's arguments.
     * @param bool $setEmptyOption Whether to set an empty option or not. Defaults to true.
     * @return array The options array as passed to the function, so it can be used as return value for Contao callbacks.
     */
    protected function dispatchFieldOptions(array $options, ?array $funcArgs = null, bool $setEmptyOption = true): array
    {
        if ($funcArgs === null) {
            $backtrace = debug_backtrace(0, 2);
            $funcArgs = $backtrace[1]['args'];
        }

        $event = !is_array($funcArgs) ?: reset($funcArgs);

        if ($event instanceof FieldOptionsEvent) {
            $event->setOptionsByKeyValue($options);
            $event->setEmptyOption($setEmptyOption);
        }

        return $options;
    }

    /**
     * Shortcut for handling field options event listeners.
     *
     * Will look for a method called `get[FieldName]Options()` on a given object and call it. The called method must
     * return an array of options. The given object defaults to `$this`, but you can hand it any DataContainer.
     *
     * Example:
     * ```php
     * #[AsEventListener('huh.form_type.huh_media_library.licence.options')]
     * #[AsEventListener('huh.form_type.huh_media_library.company.options')]
     * #[AsEventListener('huh.form_type.huh_media_library.location.options')]
     * public function optionsCallback(FieldOptionsEvent $event): void
     * {
     *     $this->optionsListenerCallback($event, $this->productContainer);
     * }
     * ```
     *
     * The method name is derived from the field name, e.g. `getLicenceOptions()` for a field named `licence`.
     *
     * If you want to handle `FieldOptionsEvent` and Contao field options callbacks together, you may want to use
     * `dispatchFieldOptions()` instead.
     *
     * @param FieldOptionsEvent $event
     * @param object|null $container
     * @return void
     */
    protected function optionsListenerCallback(FieldOptionsEvent $event, object $container = null): void
    {
        if ($container === null) {
            $container = $this;
        }

        $methodName = 'get'.ucfirst(str_replace('[]', '', $event->getWidget()->name)).'Options';

        if (method_exists($container, $methodName))
        {
            $options = $container->{$methodName}();
            $event->setOptionsByKeyValue($options);
            $event->setEmptyOption(true);
        }
        else
        {
            dump('[FieldOptionsDispatcherTrait] '.get_class($this).PHP_EOL."  Method $methodName does not exist on ".get_class($container));
        }
    }
}