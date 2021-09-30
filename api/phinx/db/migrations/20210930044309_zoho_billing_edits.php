<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ZohoBillingEdits extends AbstractMigration
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
    /**
     * Migrate Up.
     */
    public function up()
    {
        $zohoUsers = $this->table('zoho_users');
        $zohoInvoices = $this->table('zoho_invoices');

        $zohoInvoices
            ->addColumn('zoho_invoice_data', 'json',
                ['after' => 'invoice_data', 'null'=>true, 'comment'=>'Zoho json response of invoice creation'])

            ->addColumn('zoho_invoice_id', 'integer',
                ['after' => 'id', 'limit'=>100, 'default' => 0])

            ->changeColumn('invoice_data', 'json',
                ['null' => true,'comment' => 'Data send from billing page used to create invoice'])
            ->update();

        $zohoUsers
            ->changeColumn('type', 'integer',
            ['default' => 0,'comment' => 'clientAdmin(1), systemAdmin(2), typist(3)'])
            ->save();

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $zohoUsers = $this->table('zoho_users');
        $zohoInvoices = $this->table('zoho_invoices');

        $zohoUsers->changeColumn('type', 'integer',
            ['default' => 0,'comment' => 'clientAdmin(0), systemAdmin(1), typist(2)'])
            ->save();

        $zohoInvoices
            ->removeColumn('zoho_invoice_data')
            ->removeColumn('zoho_invoice_id')
            ->save();
    }
}
