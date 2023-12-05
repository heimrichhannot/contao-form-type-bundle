<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\Model;

class FormContext
{
    public static function update(string $table, array $data): self
    {
        return new static(FormContextAction::UPDATE, $table, $data);
    }

    public static function create(?string $table = null): self
    {
        return new static(FormContextAction::CREATE, $table);
    }

    public static function invalid(?string $table = null): self
    {
        return new static(FormContextAction::INVALID, $table);
    }

    public function __construct(
        private FormContextAction|string $action,
        private ?string                  $table,
        private ?array                   $data = null,
    )
    {
        $this->setAction($action);
    }

    public function getAction(): FormContextAction|string
    {
        return $this->action;
    }

    public function setAction(FormContextAction|string $action): void
    {
        if ($action instanceof FormContextAction) {
            $this->action = $action;
        } else {
            $this->action = FormContextAction::tryFrom(strtolower($action)) ?: $action;
        }
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    public function isUpdateContext(): bool
    {
        return FormContextAction::UPDATE === $this->action;
    }

    public function isCreateContext(): bool
    {
        return FormContextAction::CREATE === $this->action;
    }
}