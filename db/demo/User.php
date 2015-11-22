<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 21.11.15
 * Time: 12:36
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
        return $this->hasOne(UserProfile::className(),['id'=>'id']);
    }

    public function getFullName()
    {
        return $this->profile->first_name. ' '.$this->profile->last_name;
    }


}