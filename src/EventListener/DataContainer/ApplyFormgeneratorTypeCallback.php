<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\FormFieldModel;
use Contao\FormModel;
use Contao\Message;
use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_form", target="config.onload")
 */
class ApplyFormgeneratorTypeCallback
{
    private FormgeneratorTypeCollection $formgeneratorTypeCollection;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(FormgeneratorTypeCollection $formgeneratorTypeCollection, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator)
    {
        $this->formgeneratorTypeCollection = $formgeneratorTypeCollection;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(DataContainer $dc = null): void
    {
        if (!$dc || !$dc->id) {
            return;
        }

        $formModel = FormModel::findByPk($dc->id);
        if (!$formModel->formgeneratorType) {
            return;
        }

        $type = $this->formgeneratorTypeCollection->getType($formModel->formgeneratorType);
        if (!$type) {
            return;
        }

        $type->onload($dc, $formModel);

        $formFields = FormFieldModel::findByPid($formModel->id);

        $url = $this->urlGenerator->generate('formgenerator_type_wizard', [
            'formId' => $formModel->id,
        ]);

        if (!$formFields && !empty($type->getDefaultFields($formModel))) {
            Message::addInfo($this->translator->trans('tl_form.MESSAGE.ft_field_wizard', [$url], 'contao_tl_form'));
        }
    }

}