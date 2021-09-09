<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class BillRateTATCommentInHours extends AbstractMigration
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
        $tbl = $this->table('accounts');
        $tbl->
        changeColumn('bill_rate1_TAT', 'integer',['comment' => 'in hours'])->
        changeColumn('bill_rate2_TAT', 'integer',['comment' => 'in hours'])->
        changeColumn('bill_rate3_TAT', 'integer',['comment' => 'in hours'])->
        changeColumn('bill_rate4_TAT', 'integer',['comment' => 'in hours'])->
        changeColumn('bill_rate5_TAT', 'integer',['comment' => 'in hours'])->
        save();
    }

    public function down(){
        $tbl = $this->table('accounts');
        $tbl->
        changeColumn('bill_rate1_TAT', 'integer',['comment' => ''])->
        changeColumn('bill_rate2_TAT', 'integer',['comment' => ''])->
        changeColumn('bill_rate3_TAT', 'integer',['comment' => ''])->
        changeColumn('bill_rate4_TAT', 'integer',['comment' => ''])->
        changeColumn('bill_rate5_TAT', 'integer',['comment' => ''])->
        save();
    }
}
