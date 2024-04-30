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
    private readonly TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, private readonly FormTypeCollection $formTypeCollection)
    {
        $this->translator = $translator;
    }

    public function __invoke(array $row, string $label, DataContainer $dc, array $labels): string
    {
        if ($row['formType'] && $formType = $this->formTypeCollection->getType($row['formType'])) {
            $label .= ' <span style="color:#999;padding-left:3px;">['.$this->translator->trans('tl_form.FORMTYPE.'.$formType->getType(), [], 'contao_tl_form').']</span>';
        }

        return $label;
    }

}