<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CompactViewUserSetting extends AbstractMigration
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
    public function up()
    {

        $usersTable = $this->table('users');
        if($usersTable->getColumn("default_compact_view") == null)
        {
            $usersTable->addColumn("default_compact_view", 'boolean', ['null'=>true, 'default'=>0, 'after'=>'auto_load_job'])->update();
        }

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $usersTable = $this->table('users');
        if($usersTable->getColumn("default_compact_view") != null)
        {
            $usersTable->removeColumn("default_compact_view", 'boolean', ['null'=>true, 'default'=>0, 'after'=>'auto_load_job'])->update();
        }


    }
}
