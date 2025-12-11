<?php

namespace HeimrichHannot\FormTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCallback(table: 'tl_form', target: 'list.label.label')]
class FormLabelCallbackListener
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly FormTypeCollection $formTypeCollection)
    {
    }

    public function __invoke(array $row, string $label, DataContainer $dc, array $labels): string
    {
        if ($row['formType'] && $formType = $this->formTypeCollection->getType($row['formType'])) {
            $label .= ' <span style="color:#999;padding-left:3px;">[' . $this->translator->trans('tl_form.FORMTYPE.' . $formType->getType(), [], 'contao_tl_form') . ']</span>';
        }

        return $label;
    }
}