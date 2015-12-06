<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use bubasuma\simplechat\DataProvider;
use bubasuma\simplechat\migrations\Migration;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * Class Model
 * @package bubasuma\simplechat\db
 *
 * @property string id
 * @property string sender_id
 * @property string receiver_id
 * @property string text
 * @property bool is_new
 * @property bool is_deleted_by_sender
 * @property bool is_deleted_by_receiver
 * @property string created_at
 *
 * @property-read mixed newMessages
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 *
 */
class Model extends ActiveRecord
{
    public static function tableName()
    {
        return Migration::TABLE_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'text', 'created_at'], 'required', 'on' => 'create']
        ];
    }

    public function getNewMessages()
    {
        return $this->hasOne(static::className(), ['sender_id' => 'contact_id'])
            ->where(['is_new' => 1])
            ->groupBy('sender_id');
    }


    /**
     * @param string $userId
     * @return ConversationQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function conversations($userId)
    {
        /**@var ConversationQuery $query * */
        $query = \Yii::createObject(ConversationQuery::className(),
            [
                get_called_class(),
                ['userId' => $userId]
            ]
        );
        return $query->with([
            'newMessages' => function ($msg) use ($userId) {
                /**@var $msg ConversationQuery * */
                $msg->andOnCondition(['receiver_id' => $userId])->select(['sender_id', 'COUNT(*) AS count']);
            }
        ]);
    }

    /**
     * @param string $userId
     * @param callable $formatter The function to be called. Class methods may also be invoked statically using this
     * function by passing [$classname, $methodname] to this parameter. Additionally class methods of an object
     * instance may be called by passing [$objectinstance, $methodname] to this parameter
     * @param int $limit
     * @return DataProvider
     */
    public static function loadConversations($userId, $formatter, $limit)
    {
        return new DataProvider([
            'query' => static::conversations($userId),
            'formatter' => $formatter,
            'key' => 'id',
            'pagination' => [
                'pageSize' => $limit,
            ]
        ]);
    }

    /**
     * @param string $userId
     * @param string $contactId
     * @return MessageQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function messages($userId, $contactId)
    {
        return \Yii::createObject(MessageQuery::className(),
            [
                get_called_class(),
                ['userId' => $userId, 'contactId' => $contactId]
            ]
        );
    }

    /**
     * @param string $userId
     * @param string $contactId
     * @param callable $formatter The function to be called. Class methods may also be invoked statically using this
     * function by passing [$classname, $methodname] to this parameter. Additionally class methods of an object
     * instance may be called by passing [$objectinstance, $methodname] to this parameter
     * @param int $limit
     * @return DataProvider
     */
    public static function loadMessages($userId, $contactId, $formatter, $limit)
    {
        return new DataProvider([
            'query' => static::messages($userId, $contactId),
            'formatter' => $formatter,
            'pagination' => [
                'pageSize' => $limit,
            ]
        ]);
    }

    /**
     * @param string $userId
     * @param string $contactId
     * @return array the number of rows updated
     */
    public static function deleteConversation($userId, $contactId)
    {
        $count = static::updateAll(
            [
                'is_deleted_by_sender' => new Expression('IF([[sender_id]] =:userId, 1, is_deleted_by_sender)'),
                'is_deleted_by_receiver' => new Expression('IF([[receiver_id]] =:userId, 1, is_deleted_by_receiver)')
            ],
            ['or',
                [
                    'receiver_id' => $userId,
                    'sender_id' => $contactId,
                    'is_deleted_by_receiver' => 0
                ],
                [
                    'sender_id' => $userId,
                    'receiver_id' => $contactId,
                    'is_deleted_by_sender' => 0
                ],
            ],
            [
                ':userId' => $userId
            ]
        );
        return compact('count');
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array the number of rows updated
     */
    public static function markConversationAsRead($userId, $contactId)
    {
        $count = static::updateAll(
            ['is_new' => 0,],
            ['receiver_id' => $userId, 'sender_id' => $contactId, 'is_new' => 1]
        );
        return compact('count');
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array
     */
    public static function markConversationAsUnread($userId, $contactId)
    {
        /** @var self $last_received_message */
        $last_received_message = static::find()
            ->where(['sender_id' => $contactId, 'receiver_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
        $count = 0;
        if ($last_received_message) {
            $last_received_message->is_new = 1;
            $count = intval($last_received_message->update());
        }
        return compact('count');
    }


    /**
     * @param string $userId
     * @param string $contactId
     * @param string $text
     * @return array|bool returns true on success or errors if validation fails
     */
    public static function create($userId, $contactId, $text)
    {
        $instance = new static(['scenario' => 'create']);
        $instance->created_at = new Expression('UTC_TIMESTAMP()');
        $instance->sender_id = $userId;
        $instance->receiver_id = $contactId;
        $instance->text = Html::encode($text);
        $instance->save();
        return $instance->errors;
    }

}