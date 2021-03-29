<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MigrationScriptDBUpdates extends AbstractMigration
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
        $table = $this->table('maintenance_log', ['id' => 'maint_id']);
        $table->addColumn('maint_table', 'string', ['limit'=>250, 'null'=>true])
            ->addColumn('maint_recs_affected', 'integer', ['default'=>0, 'null'=>true])
            ->addColumn('maint_comments', 'string', ['null'=>true, 'limit'=>250])
            ->addColumn('timestamp', 'timestamp', ['default'=>'CURRENT_TIMESTAMP'])
            ->create();

        $filesTable = $this->table('files');
        if($filesTable->getColumn("deleted_date") == null)
        {
            $filesTable->addColumn("deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'deleted']);
        }

        if($filesTable->getColumn("audio_deleted_date") == null)
        {
            $filesTable->addColumn("audio_deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'deleted_date'])->update();
        }

    }
}
