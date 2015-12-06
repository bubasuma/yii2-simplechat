<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\db\Model;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

/**
 * Class DefaultController
 * @package bubasuma\simplechat\controllers
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class DefaultController extends Controller
{
    public $layout = 'main';
    public $modelClass = 'bubasuma\simplechat\db\Model';
    public $user;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->user = \Yii::$app->user->identity;
    }


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
            [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index' => ['get'],
                    'messages' => ['post'],
                    'create-message' => ['post', 'put'],
                    'conversations' => ['post'],
                    'delete-conversation' => ['delete'],
                    'mark-conversation-as-read' => ['patch'],
                    'mark-conversation-as-unread' => ['patch'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionConversations()
    {
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'loadConversations'];
        $formatter = [$this, 'formatConversation'];
        $limit = \Yii::$app->request->post('limit');
        return call_user_func($callable, $userId, $formatter, $limit);
    }

    public function actionMessages($contactId)
    {
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'loadMessages'];
        $formatter = [$this, 'formatMessage'];
        $limit = \Yii::$app->request->post('limit');
        return call_user_func($callable, $userId, $contactId, $formatter, $limit);
    }

    public function actionCreateMessage($contactId)
    {
        $userId = $this->user->id;
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
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'deleteConversation'];
        return call_user_func($callable, $userId, $contactId);
    }

    public function actionMarkConversationAsRead($contactId)
    {
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'markConversationAsRead'];
        return call_user_func($callable, $userId, $contactId);
    }

    public function actionMarkConversationAsUnread($contactId)
    {
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'markConversationAsUnRead'];
        return call_user_func($callable, $userId, $contactId);
    }

    /**
     * @param array|Model $model
     * @return array
     */
    public function formatMessage($model)
    {
        return $model;
    }

    /**
     * @param array $model
     * @return array
     */
    public function formatConversation($model)
    {
        $model['new_messages'] = ArrayHelper::getValue($model, 'newMessages.count', 0);
        ArrayHelper::remove($model, 'newMessages');
        return $model;
    }
}
