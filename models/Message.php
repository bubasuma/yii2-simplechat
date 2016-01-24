<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\models;

use bubasuma\simplechat\db\Model;
use yii\db\ActiveQuery;


/**
 * Class Message
 * @package bubasuma\simplechat\models
 *
 * @property-read User contact
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class Message extends Model
{
    public function getContact()
    {
        return $this->hasOne(User::className(), ['id' => 'contact_id']);
    }

    /**
     * @inheritDoc
     */
    public static function conversations($userId)
    {
        return parent::conversations($userId)->with([
            'contact' => function ($contact) {
                /**@var $contact ActiveQuery * */
                $contact->with([
                    'profile' => function ($profile) {
                        /**@var $profile ActiveQuery * */
                        $profile->select(['id', 'CONCAT_WS(\' \', first_name, last_name) AS name', 'avatar']);
                    },
                ])->select(['id']);
            },
            'newMessages' => function ($msg) use ($userId) {
                /**@var $msg ActiveQuery * */
                $msg->andOnCondition(['receiver_id' => $userId])->select(['sender_id', 'COUNT(*) AS count']);
            }
        ]);
    }


}