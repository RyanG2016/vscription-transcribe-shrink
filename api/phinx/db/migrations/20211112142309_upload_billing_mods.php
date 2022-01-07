<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UploadBillingMods extends AbstractMigration
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

        $accountTable = $this->table('accounts');
        if($accountTable->getColumn("pre_pay") == null)
        {
            $accountTable->addColumn("pre_pay", 'boolean', ['null'=>false, 'limit'=>0, 'default'=>1, 'after'=>'bill_rate5_min_pay'])->update();
        }
        if($accountTable->getColumn("comp_mins") == null)
        {
            $accountTable->addColumn("comp_mins", 'float', ['null'=>true, 'default'=>0.00, 'precision'=>'10','scale'=>'2','after'=>'pre_pay'])->update();
        }
        if($accountTable->getColumn("promo") == null)
        {
            $accountTable->addColumn("promo", 'boolean', ['null'=>true, 'limit'=> 1, 'default'=>0, 'after'=>'comp_mins'])->update();
        }
        if($accountTable->getColumn("bill_rate1") != null)
        {        
            $accountTable->changeColumn('bill_rate1', 'float' , ['null'=>false, 'default'=>1.65, 'precision'=>'10','scale'=>'2'])->update();
        }
        if($accountTable->getColumn("lifetime_minutes") != null)
        {        
            $accountTable->removeColumn('lifetime_minutes')->save();
            $accountTable->addColumn('lifetime_minutes', 'float' , ['null'=>true, 'default'=>0.00, 'precision'=>'10','scale'=>'2','after'=>'promo'])->update();
        }
        if($accountTable->getColumn("profile_id") == null)
        {        
            $accountTable->addColumn('profile_id', 'string' , ['null'=>true, 'default'=>null, 'limit'=>'50', 'after'=>'lifetime_minutes'])->update();
        }
        if($accountTable->getColumn("payment_id") == null)
        {        
            $accountTable->addColumn('payment_id', 'string' , ['null'=>true, 'default'=>null, 'limit'=>'50', 'after'=>'profile_id'])->update();
        }
    }

    public function down()
    {

        $accountTable = $this->table('accounts');
        if($accountTable->getColumn("pre_pay") != null)
        {
            $accountTable->removeColumn("pre_pay")->update();
        }
        if($accountTable->getColumn("comp_mins") != null)
        {
            $accountTable->removeColumn("comp_mins")->update();
        }
        if($accountTable->getColumn("promo") != null)
        {
            $accountTable->removeColumn("promo")->update();
        }
        if($accountTable->getColumn("payment_id") != null)
        {
            $accountTable->removeColumn("payment_id")->update();
        }
        if($accountTable->getColumn("profile_id") != null)
        {
            $accountTable->removeColumn("profile_id")->update();
        }
    }
}
