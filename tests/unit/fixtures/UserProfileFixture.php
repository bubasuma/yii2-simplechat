<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 4/17/16
 * Time: 5:18 PM
 */

namespace bubasuma\simplechat\tests\unit\fixtures;


use bubasuma\simplechat\models\User;
use yii\test\ActiveFixture;

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
        if(count($users) > 0){
            $index = 0;
            foreach ($this->getData() as $alias => $row) {
                if(isset($users[$index])){
                    break;
                }
                $row['id'] = $users[$index];
                $primaryKeys = $this->db->schema->insert($table->fullName, $row);
                $this->data[$alias] = array_merge($row, $primaryKeys);
            }
        }
    }
}