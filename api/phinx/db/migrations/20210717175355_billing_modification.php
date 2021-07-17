<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class BillingModification extends AbstractMigration
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
        $filesTable->addColumn('billed_date', 'timestamp', ['after' => 'billed', 'null' => true])->update();

        $accsTable = $this->table('accounts');
        $accsTable->addColumn('subscription_type', 'smallinteger', ['after' => 'acc_name', 'default' => 1])->update();

    }
}
