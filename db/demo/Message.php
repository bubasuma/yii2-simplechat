<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\db\demo;

use yii\db\ActiveQuery;


/**
 * Class Message
 * @package bubasuma\simplechat\db\demo
 *
 * @property-read User contact
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class Message extends \bubasuma\simplechat\db\Model
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
                    'profile' => function ($advanced) {
                        /**@var $advanced ActiveQuery * */
                        $advanced->select(['id', 'CONCAT_WS(\' \', first_name, last_name) AS full_name', 'avatar']);
                    },
                ])->select(['id', 'email']);
            }
        ]);
    }


}