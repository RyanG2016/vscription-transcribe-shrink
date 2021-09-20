<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ZohoBilling extends AbstractMigration
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

        $zohoUsers = $this->table('zoho_users');
        $zohoUsers
            ->addColumn('zoho_id', 'integer', ['limit' => 100])
            ->addColumn('uid', 'integer', ['limit' => 11])
            ->addColumn('acc_id', 'integer', ['limit' => 11, 'null' => true])
            ->addColumn('type', 'integer' , ['default' => 0, 'comment' => 'clientAdmin(0), systemAdmin(1), typist(2)'])
            ->addColumn('user_data', 'json', ['null' => true])
            ->addColumn('created_at', 'datetime' , ['default' => date("Y-m-d H:i:s")])

            ->addIndex(['zoho_id'], ['unique' => true])
            ->create();


        $zohoInvoices = $this->table('zoho_invoices');
        $zohoInvoices
            ->addColumn('invoice_number', 'string', ['limit' => 100, 'comment'=> 'searchable from zoho'])
            ->addColumn('zoho_id', 'integer', ['limit' => 100])
            ->addColumn('invoice_data', 'json' , ['null' => true])
            ->addColumn('created_at', 'datetime' , ['default' => date("Y-m-d H:i:s")])

            ->addIndex(['invoice_number'], ['unique' => true])
            ->create();

        if($this->isMigratingUp())
        {
            $zohoInvoices
                ->addForeignKey('zoho_id', 'zoho_users', 'zoho_id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->save();

            $zohoUsers
                ->addForeignKey('uid', 'users', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->addForeignKey('acc_id', 'accounts', 'acc_id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
                ->save();
        }

    }
}
