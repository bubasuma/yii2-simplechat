<?php

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
 */
class Model extends ActiveRecord
{
    public static function tableName()
    {
        return  Migration::TABLE_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'text', 'created_at'], 'required', 'on'=>'create']
        ];
    }


    /**
     * @param string $userId
     * @return ConversationQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function conversations($userId)
    {
        return \Yii::createObject(ConversationQuery::className(),
            [
                get_called_class(),
                ['userId' => $userId]
            ]
        );
    }

    /**
     * @param string $userId
     * @param callable $formatter The function to be called. Class methods may also be invoked statically using this
     * function by passing [$classname, $methodname] to this parameter. Additionally class methods of an object
     * instance may be called by passing [$objectinstance, $methodname] to this parameter
     * @param int $limit
     * @return DataProvider
     */
    public static function loadConversations($userId, $formatter, $limit){
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
     * @param string $text
     * @return array|bool returns true on success or errors if validation fails
     */
    public static function create($userId, $contactId, $text){
        $instance = new static(['scenario'=>'create']);
        $instance->created_at = new Expression('UTC_TIMESTAMP()');
        $instance->sender_id = $userId;
        $instance->receiver_id = $contactId;
        $instance->text = Html::encode($text);
        $instance->save();
        return $instance->errors;
    }

}