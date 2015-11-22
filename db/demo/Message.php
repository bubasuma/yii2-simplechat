<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 25.10.15
 * Time: 10:19
 */

namespace bubasuma\simplechat\db\demo;
use yii\db\ActiveQuery;


/**
 * Class Message
 * @package bubasuma\simplechat\db\demo
 *
 * @property User $contact
 */
class Message extends \bubasuma\simplechat\db\Model
{
    public function getContact()
    {
        return $this->hasOne(User::className(),['id'=>'contact_id']);
    }

    /**
     * @inheritDoc
     */
    public static function conversations($userId)
    {
        return parent::conversations($userId)->with([
            'contact' => function($contact){
                /**@var $contact ActiveQuery **/
                $contact->with([
                    'profile' => function($advanced){
                        /**@var $advanced ActiveQuery **/
                        $advanced->select(['id','CONCAT_WS(\' \', first_name, last_name) AS full_name', 'avatar']);
                    },
                ])->select(['id','email']);
            }
        ]);
    }


}