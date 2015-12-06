<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\db\demo;


use bubasuma\simplechat\migrations\Migration;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property string id
 * @property string email
 * @property string created_at
 *
 * Read Only attributes
 *
 * @property-read UserProfile profile
 * @property-read string fullName
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 *
 */
class User extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return Migration::TABLE_USER;
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


    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $this->created_at = new Expression('UTC_TIMESTAMP()');
        return parent::beforeSave($insert);
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'id']);
    }

    public function getFullName()
    {
        return $this->profile->first_name . ' ' . $this->profile->last_name;
    }


}