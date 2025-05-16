<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFeedbackTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('feedback', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'integer', [
                'identity' => true,
                'signed' => false
            ])
            ->addColumn('admin_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('complaint_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('suggestion_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('content', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])

            ->addForeignKey('admin_id', 'users', 'id', ['delete' => 'SET NULL'])
            ->addForeignKey('complaint_id', 'complaints', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('suggestion_id', 'suggestions', 'id', ['delete' => 'CASCADE'])

            ->create();
    }
}
