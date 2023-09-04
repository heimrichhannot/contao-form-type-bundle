<?php

namespace HeimrichHannot\FormTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_form", target="list.label.label")
 */
class FormLabelCallbackListener
{
    private TranslatorInterface $translator;
    private FormTypeCollection $formTypeCollection;

    public function __construct(TranslatorInterface $translator, FormTypeCollection $formTypeCollection)
    {
        $this->translator = $translator;
        $this->formTypeCollection = $formTypeCollection;
    }

    public function __invoke(array $row, string $label, DataContainer $dc, array $labels): string
    {
        if ($row['formType'] && $formType = $this->formTypeCollection->getType($row['formType'])) {
            $label .= ' ['.$this->translator->trans('tl_form.').']'
        }

        return $label;
    }

}