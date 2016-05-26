<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\tests\unit\fixtures;

use bubasuma\simplechat\models\User;
use yii\helpers\ArrayHelper;
use yii\test\ActiveFixture;

/**
 * Class MessageFixture
 * @package bubasuma\simplechat\tests\unit\fixtures
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
 */
class MessageFixture extends ActiveFixture
{
    public $modelClass = 'bubasuma\simplechat\models\Message';
    public $depends = ['bubasuma\simplechat\tests\unit\fixtures\UserFixture'];


    /**
     * @inheritdoc
     */
    public function load()
    {
        $this->resetTable();
        $this->data = [];
        $table = $this->getTableSchema();
        $data = $this->getData();
        $users = User::find()->select(['id'])->column();
        if (count($users) >= 2) {
            ArrayHelper::multisort($data, 'timestamp', SORT_ASC, SORT_NUMERIC);
            foreach ($data as $alias => $row) {
                shuffle($users);
                $row['sender_id'] = $users[0];
                $row['receiver_id'] = $users[1];
                $row['created_at'] = date('Y-m-d H:i:s', $row['timestamp']);
                unset($row['timestamp']);
                $primaryKeys = $this->db->schema->insert($table->fullName, $row);
                $this->data[$alias] = array_merge($row, $primaryKeys);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function unload()
    {
        parent::unload();
        $this->resetTable();
    }
}
