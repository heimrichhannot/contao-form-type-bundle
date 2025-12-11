<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\Form;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FormTypeBundle\Model\FormModel;

class FormTypeCollection
{
    private array $types = [];

    public function __construct(
        private readonly Connection $connection,
    )
    {
    }

    public function addType(AbstractFormType|FormTypeInterface $type): void
    {
        $this->types[$type->getType()] = $type;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(Form|string $formOrName): AbstractFormType|FormTypeInterface|null
    {
        return is_string($formOrName)
            ? $this->getTypeByName($formOrName)
            : $this->getTypeOfForm($formOrName);
    }

    public function getFormsForFormType(string $type): array
    {
        if (null === $this->getTypeByName($type)) {
            return [];
        }

        return $this->connection->createQueryBuilder()
            ->select('id', 'title')
            ->from('tl_form')
            ->where('formType = :formType')
            ->orderBy('title')
            ->setParameter('formType', $type)
            ->executeQuery()
            ->fetchAllKeyValue()
            ;

    }

    private function getTypeByName(string $type): AbstractFormType|FormTypeInterface|null
    {
        return $this->types[$type] ?? null;
    }

    private function getTypeOfForm(Form $form): AbstractFormType|FormTypeInterface|null
    {
        /** @var FormModel $model */
        $model = $form->getModel();
        return $model->formType ? $this->getTypeByName($form->formType) : null;
    }
}