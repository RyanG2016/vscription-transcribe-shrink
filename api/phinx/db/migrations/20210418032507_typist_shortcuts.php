<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TypistShortcuts extends AbstractMigration
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
//    public function change(): void
//    {
//    }

    /**
     * Migrate Up.
     */
    public function up()
    {

        $count = $this->execute('UPDATE users set shortcuts=\'[]\' where shortcuts=\'0\' or shortcuts=\'1\' or shortcuts=\'\' or shortcuts=\'{}\' ');

        $this->table('users')
            ->changeColumn('shortcuts', 'json',
                [
                    'default'=>'[]'
                ]
            )->save();

//        $count = $this->execute('UPDATE users set shortcuts=\'{}\' where shortcuts=\'0\' ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('users')
            ->changeColumn('shortcuts', 'text',
                [
                    'default'=>'0'
                ]
            )->save();

        $count = $this->execute('UPDATE users set shortcuts=\'0\' where shortcuts=\'[]\' ');

    }


}
