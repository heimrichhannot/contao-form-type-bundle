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
     * @return array The options array as passed to the function, so you can use it as return value with Symfony event listeners.
     */
    protected function dispatchFieldOptions(array $options, ?array $funcArgs = null, bool $setEmptyOption = true): array
    {
        if ($funcArgs === null) {
            $backtrace = debug_backtrace(0, 2);
            $funcArgs = $backtrace[1]['args'];
        }

        $fieldOptionsEvents = array_filter($funcArgs, function ($arg) {
            return $arg instanceof FieldOptionsEvent;
        });

        foreach ($fieldOptionsEvents as $event) {
            $event->setOptionsByKeyValue($options);
            $event->setEmptyOption($setEmptyOption);
        }

        return $options;
    }
}