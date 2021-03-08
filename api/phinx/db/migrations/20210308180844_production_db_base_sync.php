<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProductionDbBaseSync extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('cities')->drop()->save();

        $this->table('tokens')
            ->changeColumn('token_type', 'integer',
                ['limit'=>11,
                    'default'=>\Phinx\Util\Literal::from('4'),
                    'comment'=>'4:pwd reset, 5:verify account, 7: verify account + accept typist invite with accID in ext1'])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('tokens')
            ->changeColumn('token_type', 'integer',
                ['default'=>\Phinx\Util\Literal::from('4'),
                    'limit'=>11,
                    'comment'=>'4:pwd reset, 5:verify account'])
            ->save();

        $users = $this->table('cities', ['id', 'id']);
        $users->addColumn('country', 'integer', ['limit' => 11, 'comment'=>'0: America, 1: Canada', 'default'=>4])
            ->addColumn('city', 'string', ['limit' => 50])
            ->save();

        $builder = $this->getQueryBuilder();
        $builder->insert(['id', 'country', 'city'])->into("cities")
            ->values(['id' => 14, 'country'=>204, 'city'=> 'Alabama'])
            ->values(['id' => 15, 'country'=>204, 'city'=> 'Alaska'])
            ->values(['id' => 16, 'country'=>204, 'city'=> 'Samoa'])
            ->values(['id' => 17, 'country'=>204, 'city'=> 'Arizona'])
            ->values(['id' => 18, 'country'=>204, 'city'=> 'Arkansas'])
            ->values(['id' => 19, 'country'=>204, 'city'=> 'California'])
            ->values(['id' => 20, 'country'=>204, 'city'=> 'Colorado'])
            ->values(['id' => 21, 'country'=>204, 'city'=> 'Connecticut'])
            ->values(['id' => 22, 'country'=>204, 'city'=> 'Delaware'])
            ->values(['id' => 23, 'country'=>204, 'city'=> 'Columbia'])
            ->values(['id' => 24, 'country'=>204, 'city'=> 'Florida'])
            ->values(['id' => 25, 'country'=>204, 'city'=> 'Georgia'])
            ->values(['id' => 26, 'country'=>204, 'city'=> 'Guam'])
            ->values(['id' => 27, 'country'=>204, 'city'=> 'Hawaii'])
            ->values(['id' => 28, 'country'=>204, 'city'=> 'Idaho'])
            ->values(['id' => 29, 'country'=>204, 'city'=> 'Illinois'])
            ->values(['id' => 30, 'country'=>204, 'city'=> 'Indiana'])
            ->values(['id' => 31, 'country'=>204, 'city'=> 'Iowa'])
            ->values(['id' => 32, 'country'=>204, 'city'=> 'Kansas'])
            ->values(['id' => 33, 'country'=>204, 'city'=> 'Kentucky'])
            ->values(['id' => 34, 'country'=>204, 'city'=> 'Louisiana'])
            ->values(['id' => 35, 'country'=>204, 'city'=> 'Maine'])
            ->values(['id' => 36, 'country'=>204, 'city'=> 'Maryland'])
            ->values(['id' => 37, 'country'=>204, 'city'=> 'Massachusetts'])
            ->values(['id' => 38, 'country'=>204, 'city'=> 'Michigan'])
            ->values(['id' => 39, 'country'=>204, 'city'=> 'Minnesota'])
            ->values(['id' => 40, 'country'=>204, 'city'=> 'Mississippi'])
            ->values(['id' => 41, 'country'=>204, 'city'=> 'Missouri'])
            ->values(['id' => 42, 'country'=>204, 'city'=> 'Montana'])
            ->values(['id' => 43, 'country'=>204, 'city'=> 'Nebraska'])
            ->values(['id' => 44, 'country'=>204, 'city'=> 'Nevada'])
            ->values(['id' => 45, 'country'=>204, 'city'=> 'New Hampshire'])
            ->values(['id' => 46, 'country'=>204, 'city'=> 'New Jersey'])
            ->values(['id' => 47, 'country'=>204, 'city'=> 'New Mexico'])
            ->values(['id' => 48, 'country'=>204, 'city'=> 'New York'])
            ->values(['id' => 49, 'country'=>204, 'city'=> 'North Carolina'])
            ->values(['id' => 50, 'country'=>204, 'city'=> 'North Dakota'])
            ->values(['id' => 51, 'country'=>204, 'city'=> 'Northern Marianas Islands'])
            ->values(['id' => 52, 'country'=>204, 'city'=> 'Ohio'])
            ->values(['id' => 53, 'country'=>204, 'city'=> 'Oklahoma'])
            ->values(['id' => 54, 'country'=>204, 'city'=> 'Oregon'])
            ->values(['id' => 55, 'country'=>204, 'city'=> 'Pennsylvania'])
            ->values(['id' => 56, 'country'=>204, 'city'=> 'Puerto Rico'])
            ->values(['id' => 57, 'country'=>204, 'city'=> 'Rhode Island'])
            ->values(['id' => 58, 'country'=>204, 'city'=> 'South Carolina'])
            ->values(['id' => 59, 'country'=>204, 'city'=> 'South Dakota'])
            ->values(['id' => 60, 'country'=>204, 'city'=> 'Tennessee'])
            ->values(['id' => 61, 'country'=>204, 'city'=> 'Texas'])
            ->values(['id' => 62, 'country'=>204, 'city'=> 'Utah'])
            ->values(['id' => 63, 'country'=>204, 'city'=> 'Vermont'])
            ->values(['id' => 64, 'country'=>204, 'city'=> 'Virginia'])
            ->values(['id' => 65, 'country'=>204, 'city'=> 'Virgin Islands'])
            ->values(['id' => 66, 'country'=>204, 'city'=> 'Washington'])
            ->values(['id' => 67, 'country'=>204, 'city'=> 'West Virginia'])
            ->values(['id' => 68, 'country'=>204, 'city'=> 'Wisconsin'])
            ->values(['id' => 69, 'country'=>204, 'city'=> 'Wyoming'])
            ->values(['id' => 70, 'country'=>203, 'city'=> 'Alberta'])
            ->values(['id' => 71, 'country'=>203, 'city'=> 'British Columbia'])
            ->values(['id' => 72, 'country'=>203, 'city'=> 'Manitoba'])
            ->values(['id' => 73, 'country'=>203, 'city'=> 'New Brunswick'])
            ->values(['id' => 74, 'country'=>203, 'city'=> 'Newfoundland And Labrador'])
            ->values(['id' => 75, 'country'=>203, 'city'=> 'Northwest Territories'])
            ->values(['id' => 76, 'country'=>203, 'city'=> 'Nova Scotia'])
            ->values(['id' => 77, 'country'=>203, 'city'=> 'Nunavut'])
            ->values(['id' => 78, 'country'=>203, 'city'=> 'Ontario'])
            ->values(['id' => 79, 'country'=>203, 'city'=> 'Prince Edward Island'])
            ->values(['id' => 80, 'country'=>203, 'city'=> 'Quebec'])
            ->values(['id' => 81, 'country'=>203, 'city'=> 'Saskatchewan'])
            ->values(['id' => 82, 'country'=>203, 'city'=> 'Yukon'])

            ->execute();

    }
}
