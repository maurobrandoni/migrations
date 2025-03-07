<?php

use Migrations\BaseMigration;

// This NEEDs to be the new BaseMigration class to check if rollbacks work correctly with the new builtin backend
class ChangeTestTable extends BaseMigration
{
    public function change(): void
    {
        $this->table('test')
            ->addColumn('name', 'string')
            ->save();
    }
}
