<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

class FormContext
{
    public static function create(?string $table = null): self
    {
        return new static(FormContextAction::CREATE, $table);
    }

    public static function read(string $table, array $data): self
    {
        return new static(FormContextAction::READ, $table, $data);
    }

    public static function update(string $table, array $data): self
    {
        return new static(FormContextAction::UPDATE, $table, $data);
    }

    public static function delete(string $table, ?array $data = null): self
    {
        return new static(FormContextAction::DELETE, $table, $data);
    }

    public static function clone(string $table, array $data = null): self
    {
        return new static(FormContextAction::CLONE, $table, $data);
    }

    public static function invalid(?string $table = null, mixed $detail = null, ?array $moreData = []): self
    {
        return new static(FormContextAction::INVALID, $table, [
            '_detail' => $detail,
            ...$moreData,
        ]);
    }

    public function __construct(
        private FormContextAction|string $action,
        private ?string $table,
        private ?array $data = null,
    ) {
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

    public function isCreate(): bool
    {
        return FormContextAction::CREATE === $this->action;
    }

    public function isRead(): bool
    {
        return FormContextAction::READ === $this->action;
    }

    public function isUpdate(): bool
    {
        return FormContextAction::UPDATE === $this->action;
    }

    public function isDelete(): bool
    {
        return FormContextAction::DELETE === $this->action;
    }

    public function isInvalid(): bool
    {
        return FormContextAction::INVALID === $this->action;
    }
}