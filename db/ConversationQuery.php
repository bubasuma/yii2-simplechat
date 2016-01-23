<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\db;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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

        $this->select(['ms.contact_id', 'm.id', 'm.sender_id', 'm.text', 'm.created_at'])
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
        } else {
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