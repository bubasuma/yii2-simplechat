<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 21.11.15
 * Time: 12:36
 */

namespace frontend\modules\simplechat\db\demo;


use frontend\modules\simplechat\migrations\Migration;
use yii\db\ActiveRecord;

/**
 * Class UserProfile
 * @package frontend\modules\simplechat\db\demo
 *
 * @property string id
 * @property string first_name
 * @property string last_name
 * @property string gender
 * @property string avatar
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