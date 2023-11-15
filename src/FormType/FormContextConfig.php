<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\FrontendUser;
use Contao\Model;
use Symfony\Component\HttpFoundation\Request;

class FormContextConfig
{
    private bool $allowGuest = false;
    private array $memberGroups = [];
    private FormContextEvaluatorInterface $evaluator;

    public static function getDefaultContextEvaluator(): FormContextEvaluatorInterface
    {
        return new class implements FormContextEvaluatorInterface {
            public function __invoke(FormContextConfig $config, Request $request): FormContext
            {
                $editParameter = $config->getEditParameter();

                if ($request->query->has($editParameter))
                {
                    /** @var class-string<Model> $modelClass */
                    $modelClass = Model::getClassFromTable($config->getTable());
                    $modelPk = $request->query->get($editParameter);
                    return FormContext::edit($modelClass::findByPk($modelPk));
                }

                return FormContext::create();
            }
        };
    }

    public function __construct(
        private readonly string       $table,
        private readonly string       $editParameter = 'edit',
        FormContextEvaluatorInterface $contextEvaluator = null
    )
    {
        $this->evaluator = $contextEvaluator ?? static::getDefaultContextEvaluator();
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getEditParameter(): string
    {
        return $this->editParameter;
    }

    public function setAllowGuest(bool $allowGuest): void
    {
        $this->allowGuest = $allowGuest;
    }

    public function isGuestAllowed(): bool
    {
        return $this->allowGuest;
    }

    public function setMemberGroups(array $memberGroups): void
    {
        $this->memberGroups = $memberGroups;
    }

    public function getMemberGroups(): array
    {
        return $this->memberGroups;
    }

    public function setEvaluator(FormContextEvaluatorInterface $evaluator): void
    {
        $this->evaluator = $evaluator;
    }

    public function getEvaluator(): FormContextEvaluatorInterface
    {
        return $this->evaluator;
    }

    public function evaluate(Request $request): FormContext
    {
        return $this->getEvaluator()($this, $request);
    }

    public function isAllowed(FrontendUser $user, string $table, int $id): bool
    {
        if (!isset($this->allowEdit) || !$this->allowEdit) {
            return false;
        }

        if (isset($this->tables) && !in_array($table, $this->tables)) {
            return false;
        }

        $groups = $user->allGroups;
        $groups2 = $user->groups;
        $groups3 = $user->getRoles();

        if (isset($this->memberGroups) && !in_array($id, $this->memberGroups)) {
            return false;
        }

        if (isset($this->callback) && is_callable($this->callback)) {
            return call_user_func($this->callback, $user, $table, $id);
        }

        return true;
    }
}