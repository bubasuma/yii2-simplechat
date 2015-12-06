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
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'email' => 'VARCHAR(127) NOT NULL',
            'created_at' => 'DATETIME NOT NULL',
        ], $this->tableOptions);
        $tableName = $this->db->getSchema()->getRawTableName(self::TABLE_USER);
        $this->createIndex("idx-$tableName-email", self::TABLE_USER, 'email', true);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER);
    }

}
