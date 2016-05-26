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
            'id' => $this->bigPrimaryKey()->unsigned(),
            'sender_id' => $this->integer()->unsigned()->notNull(),
            'receiver_id' => $this->integer()->unsigned()->notNull(),
            'text' => $this->string(1020)->notNull(),
            'is_new' => $this->boolean()->defaultValue(true),
            'is_deleted_by_sender' => $this->boolean()->defaultValue(false),
            'is_deleted_by_receiver' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $tableName = $this->db->getSchema()->getRawTableName(self::TABLE_MESSAGE);
        $this->addForeignKey(
            "fk-$tableName-sender_id",
            self::TABLE_MESSAGE,
            'sender_id',
            self::TABLE_USER,
            'id',
            'NO ACTION',
            'CASCADE'
        );
        $this->addForeignKey(
            "fk-$tableName-receiver_id",
            self::TABLE_MESSAGE,
            'receiver_id',
            self::TABLE_USER,
            'id',
            'NO ACTION',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable(self::TABLE_MESSAGE);
    }
}
