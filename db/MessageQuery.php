<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use yii\db\ActiveQuery;

/**
 * Class MessageQuery
 * @package bubasuma\simplechat\db
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class MessageQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->alias('m');
    }

    /**
     * @param int $userId
     * @param int $contactId
     * @return $this
     * @since 2.0
     */
    public function between($userId, $contactId)
    {
        return $this->andWhere(['or',
            ['sender_id' => $contactId, 'receiver_id' => $userId, 'is_deleted_by_receiver' => false],
            ['sender_id' => $userId, 'receiver_id' => $contactId, 'is_deleted_by_sender' => false],
        ]);
    }
}
