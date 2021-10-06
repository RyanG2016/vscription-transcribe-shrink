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
        $this->table('zoho_invoices')->drop()->save();
        $this->table('zoho_users')->drop()->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
