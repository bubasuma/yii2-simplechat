<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\tests\unit\fixtures;

use yii\test\ActiveFixture;

/**
 * Class UserFixture
 * @package bubasuma\simplechat\tests\unit\fixtures
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
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
