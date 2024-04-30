<?php

namespace HeimrichHannot\FormTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Message;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_form", target="config.onload")
 */
class ApplyFormTypeCallback
{
    private readonly TranslatorInterface $translator;

    private readonly UrlGeneratorInterface $urlGenerator;

    public function __construct(
        private readonly FormTypeCollection $formTypeCollection,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $formModel = FormModel::findByPk($dc->id);
        if (!$formModel->formType) {
            return;
        }

        $type = $this->formTypeCollection->getType($formModel->formType);
        if (!$type) {
            return;
        }

        $type->onload($dc, $formModel);

        $formFields = FormFieldModel::findByPid($formModel->id);

        $url = $this->urlGenerator->generate('form_type_wizard', [
            'formId' => $formModel->id,
        ]);

        if (!$formFields && !empty($type->getDefaultFields($formModel))) {
            Message::addInfo($this->translator->trans('tl_form.MESSAGE.ft_field_wizard', [$url], 'contao_tl_form'));
        }
    }
}