<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use bubasuma\simplechat\migrations\Migration;

/**
 * Class m151121_105223_user_table
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class m151121_105223_user_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_USER, [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER);
    }
}
