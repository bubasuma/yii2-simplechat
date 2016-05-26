<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use bubasuma\simplechat\DataProvider;
use bubasuma\simplechat\migrations\Migration;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * Class Model
 * @package bubasuma\simplechat\db
 *
 * @property string id
 * @property int sender_id
 * @property int receiver_id
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
class Message extends ActiveRecord
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

    /**
     * @param int $userId
     * @param int $contactId
     * @param int $limit
     * @param bool $history
     * @param int $key
     * @return DataProvider
     * @since 2.0
     */
    public static function get($userId, $contactId, $limit, $history = true, $key = null)
    {
        $query = static::baseQuery($userId, $contactId);
        if (null !== $key) {
            $query->andWhere([$history ? '<' : '>', 'id', $key]);
        }
        return new DataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit
            ]
        ]);
    }

    /**
     * @param int $userId
     * @param int $contactId
     * @return MessageQuery
     */
    protected static function baseQuery($userId, $contactId)
    {
        return static::find()
            ->between($userId, $contactId)
            ->orderBy(['id' => SORT_DESC]);
    }


    /**
     * @param int $userId
     * @param int $contactId
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

    /**
     * @return MessageQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(MessageQuery::className(), [get_called_class()]);
    }
}
