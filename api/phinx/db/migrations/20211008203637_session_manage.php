<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SessionManage extends AbstractMigration
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

        $sessions_source_ref = $this->table('sessions_source_ref',  ['id' => false, 'primary_key' => ['src']]);
        $sessions_source_ref
            ->addColumn('src', 'integer', ['limit' => 2])
            ->addColumn('desc', 'string', ['limit' => 100])
            ->create();

        $sessions = $this->table('sessions');
        $sessions
            ->addColumn('uid', 'integer', ['limit' => 11])
            ->addColumn('php_sess_id', 'string', ['limit' => 200, 'comment'=> 'php session file name'])
            ->addColumn('src', 'integer', ['default' => 0, 'comment'=>'Source of login'])
            ->addColumn('revoked',  'integer', ['default'=>0, 'limit'=>1])
            ->addColumn('revoke_date', 'datetime', ['null'=>true])
            ->addColumn('login_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('expire_time', 'datetime', ['default' => \Phinx\Util\Literal::from('(CURRENT_TIMESTAMP + INTERVAL 1 DAY)')])
            ->addColumn('ip_address', 'string', ['null' => true])

            ->addForeignKey('uid', 'users', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->addForeignKey('src', 'sessions_source_ref', 'src', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();



        if($this->isMigratingUp())
        {
            // inserting multiple rows
            $rows = [
                [
                    'src'    => 0,
                    'desc'  => 'Website'
                ],
                [
                    'src'    => 1,
                    'desc'  => 'API'
                ]
            ];

            $this->table('sessions_source_ref')->insert($rows)->save();
        }

    }

}
