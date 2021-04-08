<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FeatureJobRefresh extends AbstractMigration
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
		$accountsTable = $this->table('accounts');
        if($accountsTable->getColumn("auto_list_refresh") == null)
        {
            $accountsTable->addColumn("auto_list_refresh", 'integer', ['null'=>true, 'default'=>1, 'after'=>'sr_enabled']);
        }

        if($accountsTable->getColumn("auto_list_refresh_interval") == null)
        {
            $accountsTable->addColumn("auto_list_refresh_interval", 'integer', ['null'=>true, 'default'=>30, 'after'=>'auto_list_refresh'])->update();
        }

    }
}
