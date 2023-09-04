<?php

namespace HeimrichHannot\FormTypeBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\FormFieldModel;
use Contao\FormModel;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/contao/form-type/fieldwizard/", name="form_type_wizard", defaults={"_scope" = "backend", "_token_check" = true})
 */
class CreateDefaultFormController extends AbstractController
{
    private Security $security;
    private FormTypeCollection $formTypeCollection;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private ParameterBagInterface $parameterBag;
    private TranslatorInterface $translator;

    public function __construct(Security $security, FormTypeCollection $formTypeCollection, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, ParameterBagInterface $parameterBag, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->formTypeCollection = $formTypeCollection;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->parameterBag = $parameterBag;
        $this->translator = $translator;
    }


    public function __invoke(Request $request): Response
    {
        if (!$this->security->isGranted('contao_user.modules', 'form')) {
            return new Response('Access denied', 403);
        }

        $formId = $request->query->get('formId');
        $formModel = FormModel::findByPk($formId);
        if (!$formModel) {
            return new Response('Form not found', 404);
        }

        if (!$this->security->isGranted('contao_user.forms', $formModel->id)) {
            return new Response('Access denied', 403);
        }

        $formFields = FormFieldModel::findByPid($formModel->id);
        if ($formFields) {
            return new Response('Form already has fields', 400);
        }

        $type = $this->formTypeCollection->getType($formModel->formType);
        if (!$type) {
            return new Response('Form type not found', 404);
        }

        $fields = $type->getDefaultFields($formModel);
        if (!$fields) {
            return new Response('No default fields found', 404);
        }

        $sorting = 0;
        $addSubmit = true;
        foreach ($fields as $field) {
            $row = array_merge($field, [
                'tstamp' => time(),
                'pid' => $formModel->id,
                'sorting' => $sorting++,
            ]);

            if ($row['type'] === 'submit') {
                $addSubmit = false;
            }

            $formFieldModel = new FormFieldModel();
            $formFieldModel->setRow($row);
            $formFieldModel->save();
        }

        if ($addSubmit) {
            $formFieldModel = new FormFieldModel();
            $formFieldModel->setRow([
                'tstamp' => time(),
                'pid' => $formModel->id,
                'sorting' => $sorting++,
                'type' => 'submit',
                'slabel' => $this->translator->trans('MSC.FORMTYPE.FORM.submit', [], 'contao_default'),
            ]);
            $formFieldModel->save();
        }

        return new RedirectResponse( $this->generateUrl(
            'contao_backend',
            [
                'do' => 'form',
                'table' => 'tl_form_field',
                'id' => $formModel->id,
                'ref' => $request->get('_contao_referer_id'),
                'rt' => $this->csrfTokenManager->getToken($this->parameterBag->get('contao.csrf_token_name'))->getValue(),
            ]
        ));
    }
}