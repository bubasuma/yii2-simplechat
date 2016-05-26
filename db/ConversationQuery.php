<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Class ConversationQuery
 * @package bubasuma\simplechat\db
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class ConversationQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->alias('c');
        $this->select([
                 'last_message_id' => new Expression('MAX([[id]])'),
                 'contact_id' => new Expression('IF([[sender_id]] = :userId, [[receiver_id]], [[sender_id]])')
            ])
            ->andWhere(['or',
                ['receiver_id' => new Expression(':userId'), 'is_deleted_by_receiver' => false],
                ['sender_id' => new Expression(':userId'), 'is_deleted_by_sender' => false],
            ])
            ->groupBy(['contact_id']);
    }

    /**
     * @param int $userId
     * @return $this
     * @since 2.0
     */
    public function forUser($userId)
    {
        return $this->addParams(['userId' => $userId]);
    }
}
