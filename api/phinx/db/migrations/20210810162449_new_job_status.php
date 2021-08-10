<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewJobStatus extends AbstractMigration
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
        // execute()
        $count = $this->execute('insert into file_status_ref(j_status_id, j_status_name) values (12, "In Typist Queue")'); 
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
