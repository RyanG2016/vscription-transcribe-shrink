<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ZohoBills extends AbstractMigration
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


        $zohoInvoices = $this->table('zoho_bills');
        $zohoInvoices
            ->addColumn('bill_number', 'string', ['limit' => 12, 'comment'=> 'manually counted'])

            ->addColumn('zoho_contact_id',
                'string', ['limit' => 100 , 'comment'=>'Zoho contact id of vendor(contact) not contact-person id', 'default' => ''])

            ->addColumn('zoho_bill_id', 'string',['after' => 'id', 'limit'=>100, 'default' => ''])
            ->addColumn('local_bill_data', 'json' , ['null' => true, 'comment'=>'Local request data for issues debugging'])
            ->addColumn('zoho_bill_data', 'json',
                ['null'=>true, 'comment'=>'Zoho json response of bill creation'])

            ->addColumn('created_at', 'datetime' , ['default' => date("Y-m-d H:i:s")])

            ->addIndex(['zoho_bill_id'], ['unique' => true])
            ->create();

    }
}
