<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class ConversationQuery
 * @package bubasuma\simplechat\db
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class ConversationQuery extends ActiveQuery
{
    public $userId;

    public function init()
    {
        parent::init();
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $subQuery = (new Query())
            ->from($tableName)
            ->select([
                 'message_id' => new Expression('MAX([[id]])'),
                 'contact_id' => new Expression('IF([[sender_id]] = :userId, [[receiver_id]], [[sender_id]])')
            ])
            ->where([
                'or',
                ['receiver_id' => $this->userId, 'is_deleted_by_receiver' => 0],
                ['sender_id' => $this->userId, 'is_deleted_by_sender' => 0],
            ])
            ->params([':userId' => $this->userId])
            ->groupBy(['contact_id']);

        $this->alias('m')
            ->select(['c.contact_id', 'm.id', 'm.sender_id', 'm.text', 'm.created_at'])
            ->innerJoin(['c' => $subQuery], 'c.message_id = m.id')
            ->orderBy(['c.message_id' => SORT_DESC])
            ->params([':userId' => $this->userId])
            ->asArray();
    }
}