<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\models;

use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\StringHelper;
use yii\helpers\Url;


/**
 * Class Conversation
 * @package bubasuma\simplechat\models
 *
 * @property-read User contact
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
 */
class Conversation extends \bubasuma\simplechat\db\Conversation
{
    /**
     * @return ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(User::className(), ['id' => 'contact_id']);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            'lastMessage' => function($model){
                return [
                    'text' => StringHelper::truncate($model['lastMessage']['text'], 20),
                    'date' => static::formatDate($model['lastMessage']['created_at']),
                    'sender_id' => $model['lastMessage']['sender_id']
                ];
            },
            'newMessages' => function($model){
                return [
                    'count' => empty($model['newMessages']) ? 0 :$model['newMessages']['count']
                ];
            },
            'contact' => function($model){
                return $model['contact'];
            },
            'loadUrl',
            'sendUrl',
            'deleteUrl',
            'readUrl',
            'unreadUrl',
        ];
    }

    /**
     * @inheritDoc
     */
    protected static function baseQuery($userId){
        return parent::baseQuery($userId)->with([
            'contact.profile',
            'newMessages' => function ($msg) use ($userId) {
                /**@var $msg ActiveQuery * */
                $msg->andOnCondition(['receiver_id' => $userId])
                    ->select(['sender_id', 'count' => new Expression('COUNT(*)')])
                    ->groupBy(['sender_id']);
            }]);
    }

    public static function formatDate($value)
    {
        $today = date_create()->setTime(0, 0, 0);
        $date = date_create($value)->setTime(0, 0, 0);
        if ($today == $date) {
            $formatted = \Yii::$app->formatter->asTime($value, 'short');
        } else if ($today->getTimestamp() - $date->getTimestamp() == 24 * 60 * 60) {
            $formatted = 'Yesterday';
        } else if ($today->format('W') == $date->format('W') && $today->format('Y') == $date->format('Y')) {
            $formatted = \Yii::$app->formatter->asDate($value, 'php:l');
        } else if ($today->format('Y') == $date->format('Y')) {
            $formatted = \Yii::$app->formatter->asDate($value, 'php:d F');
        } else {
            $formatted = \Yii::$app->formatter->asDate($value, 'medium');
        }
        return $formatted;
    }

    public function getLoadUrl()
    {
        return Url::to(['messages','contactId' => $this->contact_id]);
    }

    public function getSendUrl()
    {
        return Url::to(['create-message','contactId' => $this->contact_id]);
    }

    public function getDeleteUrl()
    {
        return Url::to(['delete-conversation','contactId' => $this->contact_id]);
    }

    public function getReadUrl()
    {
        return Url::to(['mark-conversation-as-read','contactId' => $this->contact_id]);
    }

    public function getUnreadUrl()
    {
        return Url::to(['mark-conversation-as-unread','contactId' => $this->contact_id]);
    }
}