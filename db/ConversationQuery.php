<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 18.10.15
 * Time: 12:43
 */

namespace frontend\modules\simplechat\db;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

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
                'MAX( [[id]] ) AS [[last_message_id]]',
                'IF( [[sender_id]] = :userId, [[receiver_id]], [[sender_id]] ) AS [[contact_id]]'
            ])
            ->where(
                ['or',
                    [
                        'receiver_id' => $this->userId,
                        'is_deleted_by_receiver' => 0
                    ],
                    [
                        'sender_id' => $this->userId,
                        'is_deleted_by_sender' => 0
                    ],
                ]
            )
            ->params([':userId' => $this->userId])
            ->groupBy(['[[contact_id]]']);

        $this->select(['contact_id', 'id', 'sender_id','is_new', 'text', 'created_at'])
            ->from(['ms' => $subQuery])
            ->innerJoin(['m' => $tableName], '[[last_message_id]] = [[id]]')
            ->orderBy(['id' => SORT_DESC])
            ->params([':userId' => $this->userId]);
    }

    /**
     * @inheritdoc
     */
    public function populate($rows)
    {
        $this->asArray = true;

        if (empty($rows)) {
            return [];
        }

        $models = [];

        if ($this->indexBy === null) {
            $models = $rows;
        }else{
            foreach ($rows as $row) {
                if (is_string($this->indexBy)) {
                    $key = $row[$this->indexBy];
                } else {
                    $key = call_user_func($this->indexBy, $row);
                }
                $models[$key] = $row;
            }
        }

        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }

        return $models;
    }
}