<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class MessageQuery
 * @package bubasuma\simplechat\db
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class MessageQuery extends ActiveQuery
{
    public $userId;
    public $contactId;

    public function init()
    {
        parent::init();
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->select(['m.id', 'm.text', 'm.created_at', 'm.is_new', 'm.sender_id'])
            ->from(['m' => $tableName])
            ->where(
                ['or',
                    [
                        'sender_id' => $this->contactId,
                        'receiver_id' => $this->userId,
                        'is_deleted_by_receiver' => 0
                    ],
                    [
                        'sender_id' => $this->userId,
                        'receiver_id' => $this->contactId,
                        'is_deleted_by_sender' => 0
                    ],
                ]
            )
            ->orderBy(['m.id' => SORT_DESC])
            ->asArray();

    }

}