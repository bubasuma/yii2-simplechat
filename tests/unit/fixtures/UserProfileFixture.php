<?php
/**
 * Created by PhpStorm.
 * User: Buba Suma
 * Date: 4/17/16
 * Time: 5:18 PM
 */

namespace bubasuma\simplechat\tests\unit\fixtures;


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
}