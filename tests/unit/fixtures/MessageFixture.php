<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 4/17/16
 * Time: 5:10 PM
 */

namespace bubasuma\simplechat\tests\unit\fixtures;


use yii\helpers\ArrayHelper;
use yii\test\ActiveFixture;

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
        ArrayHelper::multisort($data, 'timestamp', SORT_ASC, SORT_NUMERIC);
        foreach ($data as $alias => $row) {
            $row['created_at'] = date('Y-m-d H:i:s', $row['timestamp']);
            unset($row['timestamp']);
            $primaryKeys = $this->db->schema->insert($table->fullName, $row);
            $this->data[$alias] = array_merge($row, $primaryKeys);
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