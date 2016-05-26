<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\models;

use bubasuma\simplechat\migrations\Migration;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package bubasuma\simplechat\models
 *
 * @property string id
 * @property string email
 * @property string created_at
 *
 * Read Only attributes
 *
 * @property-read UserProfile profile
 * @property-read string name
 * @property-read string avatar
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    private $_name;

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
    public function fields()
    {
        return ['id', 'name', 'avatar'];
    }


    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $this->created_at = new Expression('UTC_TIMESTAMP()');
        return parent::beforeSave($insert);
    }

    /**
     * @return UserProfile
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'id']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (null === $this->_name) {
            $this->_name = $this->profile->first_name . ' ' . $this->profile->last_name;
        }
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->profile->avatar;
    }

    /**
     * @return static[]
     */
    public static function getAll()
    {
        return static::find()->with('profile')->all();
    }


    /**
     * @inheritDoc
     * @return static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException(get_called_class() . ' does not support findIdentityByAccessToken().');
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }
}
