<?php
namespace bubasuma\simplechat\tests\unit\fixtures;
use yii\test\ActiveFixture;

/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 4/17/16
 * Time: 4:57 PM
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'bubasuma\simplechat\models\User';

    /**
     * @inheritdoc
     */
    public function unload()
    {
        parent::unload();
        $this->resetTable();
    }
}