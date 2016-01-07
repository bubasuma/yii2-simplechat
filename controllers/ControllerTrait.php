<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\db\Model;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

/**
 * Trait DefaultController
 * @package bubasuma\simplechat\controllers
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 *
 * @property-read IdentityInterface user
 * @property-read string modelClass
 */
trait ControllerTrait
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => [
                    'messages',
                    'create-message',
                    'conversations',
                    'delete-conversation',
                    'mark-conversation-as-read',
                    'mark-conversation-as-unread',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionConversations()
    {
        $userId = $this->user->getId();
        $callable = [$this->modelClass, 'loadConversations'];
        $formatter = [$this, 'formatConversation'];
        $limit = \Yii::$app->request->post('limit');
        return call_user_func($callable, $userId, $formatter, $limit);
    }

    public function actionMessages($contactId)
    {
        $userId = $this->user->getId();
        $callable = [$this->modelClass, 'loadMessages'];
        $formatter = [$this, 'formatMessage'];
        $limit = \Yii::$app->request->post('limit');
        return call_user_func($callable, $userId, $contactId, $formatter, $limit);
    }

    public function actionCreateMessage($contactId)
    {
        $userId = $this->user->getId();
        if ($userId == $contactId) {
            throw new ForbiddenHttpException('You attempt to send message to yourself');
        }
        $text = \Yii::$app->request->post('text');
        return call_user_func([$this->modelClass, 'create'], $userId, $contactId, $text);
    }

    public function actionDeleteMessage($id)
    {
        throw new NotSupportedException(get_class($this) . ' does not support actionDeleteMessage().');
    }

    public function actionDeleteConversation($contactId)
    {
        $userId = $this->user->getId();
        $callable = [$this->modelClass, 'deleteConversation'];
        return call_user_func($callable, $userId, $contactId);
    }

    public function actionMarkConversationAsRead($contactId)
    {
        $userId = $this->user->getId();
        $callable = [$this->modelClass, 'markConversationAsRead'];
        return call_user_func($callable, $userId, $contactId);
    }

    public function actionMarkConversationAsUnread($contactId)
    {
        $userId = $this->user->getId();
        $callable = [$this->modelClass, 'markConversationAsUnRead'];
        return call_user_func($callable, $userId, $contactId);
    }

    /**
     * @return IdentityInterface
     */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return Model::className();
    }

    /**
     * @param array|Model $model
     * @return array|Model
     */
    abstract protected function formatMessage($model);

    /**
     * @param array $model
     * @return array
     */
    abstract protected function formatConversation($model);
}
