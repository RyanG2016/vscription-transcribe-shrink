<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class StoppingPhpServices extends AbstractMigration
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
        $table = $this->table('php_services', ['id' => 'service_id']);
        $table
            ->addColumn('service_name', 'string', ['limit' => 100])
            ->addColumn('last_start_time', 'timestamp', ['null'=>true, 'default'=>null])
            ->addColumn('last_stop_time', 'timestamp', ['null'=>true, 'default'=>null])
            ->addColumn('requests_made', 'integer', ['default'=>0])
            ->addColumn('current_status', 'integer',
                ['default'=>0, 'comment'=>'This is not a reliable indicator as it may not be updated on sudden power loss'])
            ->create();

        if ($this->isMigratingUp()) {
            $table
                ->insert([
                    [
                        'service_id' => 1,
                        'service_name' => 'Conversion'
                    ],
                    [
                        'service_id' => 2,
                        'service_name' => 'Rev.ai Submitter'
                    ],
                    [
                        'service_id' => 3,
                        'service_name' => 'Rev.ai Receiver'
                    ]
                ])
                ->save();
        }
    }
}
