<?php
use frontend\modules\simplechat\migrations\Migration;

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
        $this->createIndex("idx-$tableName-email", self::TABLE_USER, 'email',true);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER);
    }

}
