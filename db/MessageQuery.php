<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 18.10.15
 * Time: 12:42
 */

namespace frontend\modules\simplechat\db;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
        $this->select(['m.id','m.text', 'm.created_at', 'm.is_new', 'm.sender_id'])
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