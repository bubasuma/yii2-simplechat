<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\tests\unit\fixtures;

use bubasuma\simplechat\models\User;
use yii\test\ActiveFixture;

/**
 * Class UserProfileFixture
 * @package bubasuma\simplechat\tests\unit\fixtures
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
 */
class UserProfileFixture extends ActiveFixture
{
    public $modelClass = 'bubasuma\simplechat\models\UserProfile';
    public $depends = ['bubasuma\simplechat\tests\unit\fixtures\UserFixture'];

    /**
     * @inheritdoc
     */
    public function unload()
    {
        parent::unload();
        $this->resetTable();
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        $this->resetTable();
        $this->data = [];
        $table = $this->getTableSchema();
        $users = User::find()->select(['id'])->column();
        if (count($users) > 0) {
            $index = 0;
            foreach ($this->getData() as $alias => $row) {
                $row['id'] = $users[$index++];
                $this->db->schema->insert($table->fullName, $row);
                $this->data[$alias] = $row;
            }
        }
    }
}
