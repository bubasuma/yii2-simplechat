<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use bubasuma\simplechat\migrations\Migration;

/**
 * Class m151121_105453_message_table
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class m151121_105453_message_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_MESSAGE, [
            'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'sender_id' => 'BIGINT UNSIGNED NOT NULL',
            'receiver_id' => 'BIGINT UNSIGNED NOT NULL',
            'text' => ' VARCHAR(1000) NOT NULL',
            'is_new' => 'BOOLEAN DEFAULT 1',
            'is_deleted_by_sender' => 'BOOLEAN DEFAULT 0',
            'is_deleted_by_receiver' => 'BOOLEAN DEFAULT 0',
            'created_at' => 'DATETIME NOT NULL',
        ], $this->tableOptions);
        $tableName = $this->db->getSchema()->getRawTableName(self::TABLE_MESSAGE);
        $this->addForeignKey("fk-$tableName-sender_id", self::TABLE_MESSAGE, 'sender_id', self::TABLE_USER, 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey("fk-$tableName-receiver_id", self::TABLE_MESSAGE, 'receiver_id', self::TABLE_USER, 'id', 'NO ACTION', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_MESSAGE);
    }
}
