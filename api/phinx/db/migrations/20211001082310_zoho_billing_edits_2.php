<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ZohoBillingEdits2 extends AbstractMigration
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
            ->addColumn('zoho_id', 'string', ['limit' => 100, 'default' => '', 'comment'=>'zoho contact-person id'])
            ->addColumn('zoho_contact_id', 'string',
                ['limit'=>100, 'comment'=>'zoho contact id of org not contact-person id', 'default' => ''])

            ->addColumn('uid', 'integer', ['limit' => 11])
            ->addColumn('acc_id', 'integer', ['limit' => 11, 'null' => true])
            ->addColumn('type', 'integer' , ['default' => 0, 'comment' => 'systemAdmin(1), clientAdmin(2), typist(3)'])
            ->addColumn('primary_contact', 'integer' , ['default' => 0, 'limit'=>1, 'comment' => 'Admin who owns the organization'])
            ->addColumn('user_data', 'json', ['null' => true])
            ->addColumn('created_at', 'datetime' , ['default' => date("Y-m-d H:i:s")])

            ->addIndex(['zoho_id'], ['unique' => true])
            ->addForeignKey('uid', 'users', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->addForeignKey('acc_id', 'accounts', 'acc_id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();


        $zohoInvoices = $this->table('zoho_invoices');
        $zohoInvoices
            ->addColumn('invoice_number', 'string', ['limit' => 100, 'comment'=> 'searchable from zoho'])

            ->addColumn('zoho_contact_id',
                'string', ['limit' => 100 , 'comment'=>'Zoho contact id of org not contact-person id', 'default' => ''])

            ->addColumn('zoho_invoice_id', 'string',['after' => 'id', 'limit'=>100, 'default' => ''])
            ->addColumn('local_invoice_data', 'json' , ['null' => true, 'comment'=>'Local request data for issues debugging'])
            ->addColumn('zoho_invoice_data', 'json',
                        ['null'=>true, 'comment'=>'Zoho json response of invoice creation'])

            ->addColumn('created_at', 'datetime' , ['default' => date("Y-m-d H:i:s")])

//            ->addIndex(['invoice_number'], ['unique' => true])
            ->create();

    }
}
