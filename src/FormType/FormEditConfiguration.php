<?php

namespace HeimrichHannot\FormTypeBundle\FormType;

use Contao\FrontendUser;

class FormEditConfiguration
{
    private bool $allowEdit;
    private array $tables;
    private array $memberGroups;
    private $callback;

    public static function create(bool $allowEdit = false, array $tables = []): self
    {
        $instance = new self();
        $instance->allowEdit = $allowEdit;
        $instance->tables = $tables;
        return $instance;
    }

    public function setAllowEdit(bool $allowEdit): void
    {
        $this->allowEdit = $allowEdit;
    }

    public function setTables(array $tables): void
    {
        $this->tables = $tables;
    }

    public function setMemberGroups(array $memberGroups): void
    {
        $this->memberGroups = $memberGroups;
    }

    /**
     * Add custom logic to check if the current user is allowed to edit the entity.
     * Callback must return a boolean.
     *
     * @param callable $callback
     * @return void
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
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