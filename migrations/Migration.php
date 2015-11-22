<?php
namespace frontend\modules\simplechat\migrations;
/**
 * Class Migration
 *
 * @package frontend\modules\simplechat\migrations;
 * @property string $tableOptions
 *
 */
class Migration extends \yii\db\Migration {
    const TABLE_USER = '{{%user}}';
    const TABLE_USER_PROFILE = '{{%user_profile}}';
    const TABLE_MESSAGE = '{{%message}}';

    public function getTableOptions(){
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        return $tableOptions;
    }
}