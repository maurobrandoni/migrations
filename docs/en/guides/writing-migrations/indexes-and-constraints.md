# Indexes and Constraints

## Working With Indexes

To add an index to a table, call `addIndex()`:

```php
<?php

use Migrations\BaseMigration;

class MyNewMigration extends BaseMigration
{
    public function up(): void
    {
        $this->table('users')
            ->addColumn('city', 'string')
            ->addIndex(['city'])
            ->save();
    }
}
```

You can also specify unique indexes, explicit names, sort order, index length,
and advanced adapter-specific options.

The fluent builder is also available:

```php
$this->table('users')
    ->addIndex(
        $this->index(['email', 'username'])
            ->setType('unique')
            ->setName('idx_users_email')
            ->setOrder(['email' => 'DESC', 'username' => 'ASC'])
    )
    ->save();
```

Adapter-specific capabilities include:

- MySQL `fulltext` indexes
- MySQL index-length options
- SQL Server and PostgreSQL `include` columns
- PostgreSQL, SQL Server, and SQLite partial indexes
- PostgreSQL concurrent index creation
- PostgreSQL `gin` indexes

To remove indexes, use `removeIndex()` or `removeIndexByName()`.

## Working With Foreign Keys

Migrations supports foreign key constraints on database tables:

```php
<?php

use Migrations\BaseMigration;

class MyNewMigration extends BaseMigration
{
    public function up(): void
    {
        $this->table('tag_relationships')
            ->addColumn('tag_id', 'integer', ['null' => true])
            ->addForeignKey(
                'tag_id',
                'tags',
                'id',
                ['delete' => 'SET_NULL', 'update' => 'NO_ACTION']
            )
            ->save();
    }
}
```

The `delete` and `update` options control `ON DELETE` and `ON UPDATE`
behavior. Valid values are `SET_NULL`, `NO_ACTION`, `CASCADE`, and
`RESTRICT`.

Foreign keys can also be defined with arrays of columns for composite keys.

The `foreignKey()` fluent builder is available for more complex cases:

```php
$this->table('articles')
    ->addForeignKey(
        $this->foreignKey()
            ->setColumns('user_id')
            ->setReferencedTable('users')
            ->setReferencedColumns('user_id')
            ->setName('article_user_fk')
    )
    ->save();
```

Use `hasForeignKey()` to check whether a foreign key exists, and
`dropForeignKey()` to remove one.

## Working With Check Constraints

Check constraints allow you to enforce data validation rules at the database
level.

> [!NOTE]
> Check constraints are supported by MySQL 8.0.16+, PostgreSQL, and SQLite.

### Adding a Check Constraint

```php
$this->table('products')
    ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2])
    ->addCheckConstraint('price_positive', 'price > 0')
    ->save();
```

### Using the Fluent Builder

```php
$this->table('users')
    ->addCheckConstraint(
        $this->checkConstraint()
            ->setName('age_valid')
            ->setExpression('age >= 18 AND age <= 120')
    )
    ->save();
```

If you do not specify a name, one will be auto-generated.

Check constraints can reference multiple columns and use more complex SQL
expressions.

Use `hasCheckConstraint()` to verify existence and `dropCheckConstraint()` to
remove a constraint.

### Database-Specific Behavior

- MySQL stores check constraint metadata in
  `INFORMATION_SCHEMA.CHECK_CONSTRAINTS`
- PostgreSQL stores constraints in `pg_constraint`
- SQLite recreates the table when altering check constraints
- SQL Server support is planned for a future release

## Next Steps

- [Columns and Table Operations](columns-and-table-operations)
- [Schema Introspection and Platform Limitations](schema-introspection-and-platform-limitations)
