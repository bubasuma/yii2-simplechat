<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\migrations;

/**
 * Class Migration
 * @package bubasuma\simplechat\migrations
 *
 * @property-read string tableOptions
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class Migration extends \yii\db\Migration
{
    const TABLE_USER = '{{%user}}';
    const TABLE_USER_PROFILE = '{{%user_profile}}';
    const TABLE_MESSAGE = '{{%message}}';

    public function getTableOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        return $tableOptions;
    }
}
