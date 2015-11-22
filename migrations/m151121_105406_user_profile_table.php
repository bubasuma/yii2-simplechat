<?php

use bubasuma\simplechat\migrations\Migration;

class m151121_105406_user_profile_table extends Migration
{
    public function up()
    {
        $this->createTable(self::TABLE_USER_PROFILE, [
            'id' => 'BIGINT UNSIGNED PRIMARY KEY',
            'first_name' => 'VARCHAR(31) DEFAULT NULL',
            'last_name' => 'VARCHAR(31) DEFAULT NULL',
            'gender' => 'CHAR(1) DEFAULT NULL',
            'avatar' => 'VARCHAR(63) DEFAULT NULL',
        ], $this->tableOptions);
        $tableName = $this->db->getSchema()->getRawTableName(self::TABLE_USER_PROFILE);
        $this->addForeignKey("fk-$tableName-id",self::TABLE_USER_PROFILE,'id',self::TABLE_USER,'id','CASCADE','CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::TABLE_USER_PROFILE);
    }
}
