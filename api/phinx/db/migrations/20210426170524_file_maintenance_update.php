<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FileMaintenanceUpdate extends AbstractMigration
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

        $filesTable = $this->table('files');
        if($filesTable->getColumn("deleted_date") == null)
        {
            $filesTable->addColumn("deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'deleted']);
        }

        if($filesTable->getColumn("audio_file_deleted_date") == null)
        {
            $filesTable->addColumn("audio_file_deleted_date", 'timestamp', ['null'=>true, 'default'=>null, 'after'=>'audio_deleted_date'])->update();
        }
    }
}
