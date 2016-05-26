<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\models;

use bubasuma\simplechat\migrations\Migration;
use yii\db\ActiveRecord;

/**
 * Class UserProfile
 * @package bubasuma\simplechat\models
 *
 * @property string id
 * @property string first_name
 * @property string last_name
 * @property string avatar
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class UserProfile extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return Migration::TABLE_USER_PROFILE;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }
}
