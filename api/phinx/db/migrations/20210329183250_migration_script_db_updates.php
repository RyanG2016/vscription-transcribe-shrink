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
        $mainTable = $this->table('maintenance_log');
        $mainTable->renameColumn("maint_count", "maint_recs_affected");
        $mainTable->addColumn('maint_comments', 'string', ['null'=>true, 'limit'=>250, 'after' => 'maint_recs_affected'])->update();

        $filesTable = $this->table('files');
        $filesTable->addColumn("deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'deleted']);
        $filesTable->addColumn("audio_deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'deleted_date'])->update();

    }
}
