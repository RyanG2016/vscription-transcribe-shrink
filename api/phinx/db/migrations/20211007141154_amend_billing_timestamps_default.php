<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AmendBillingTimestampsDefault extends AbstractMigration
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
    public function up(): void
    {
        $zohoUsers = $this->table('zoho_users');
        $zohoUsers
            ->changeColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->save();

        $zohoInvoices = $this->table('zoho_invoices');
        $zohoInvoices
            ->changeColumn('created_at', 'datetime' , ['default' => 'CURRENT_TIMESTAMP'])
            ->save();

        $zohoBills = $this->table('zoho_bills');
        $zohoBills
            ->changeColumn('created_at', 'datetime' , ['default' => 'CURRENT_TIMESTAMP'])
            ->save();


    }

    public function down(): void
    {
        /*$zohoUsers = $this->table('zoho_users');
        $zohoUsers
            ->changeColumn('created_at', 'datetime', ['default' => date("Y-m-d H:i:s")])
            ->save();*/
    }
}
