<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\models\Message;
use bubasuma\simplechat\models\User;
use bubasuma\simplechat\helpers\DateHelper;
use bubasuma\simplechat\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package bubasuma\simplechat\controllers
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 *
 * @property-read User user
 */
class DefaultController extends Controller
{
    use ControllerTrait {
        behaviors as behaviorsTrait;
    }

    public $layout = 'main.twig';
    
    /**
     * @var Module
     */
    public $module;

    /**
     * @var User
     */
    private $_user;

    public function behaviors()
    {
        return ArrayHelper::merge($this->behaviorsTrait(), [
            [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index' => ['get', 'post'],
                    'messages' => ['get'],
                    'conversations' => ['get'],
                    'create-message' => ['post'],
                    'delete-conversation' => ['delete'],
                    'mark-conversation-as-read' => ['patch'],
                    'mark-conversation-as-unread' => ['patch'],
                ],
            ]
        ]);
    }


    public function actionIndex($contactId)
    {
        if(\Yii::$app->request->isPost){
            $this->setUser(\Yii::$app->request->get('userId'));
            return \Yii::$app->getResponse()->redirect(Url::current(['userId' => null]));
        }
        $user = $this->user;
        $contact = User::findIdentity(['id' => $contactId]);
        if (empty($contact)) {
            throw new NotFoundHttpException();
        }
        $this->view->title = $contact->name;
        $conversationDataProvider = call_user_func([$this->modelClass, 'loadConversations'],
            $user->id, [$this, 'formatConversation'], 8);

        $messageDataProvider = call_user_func([$this->modelClass, 'loadMessages'],
            $user->id, $contact->id, [$this, 'formatMessage'], 10);
        $users = [];
        foreach (User::getAll() as $userItem) {
            $users[] = [
                'label' => $userItem->name,
                'url' => Url::current(['contactId' => $contact->id]),
                'options' => ['class' => $userItem->id == $user->id ? 'disabled' : '', 'data-method' => 'post']
            ];
        }
        return $this->render('index.twig', compact('conversationDataProvider', 'messageDataProvider', 'users', 'user', 'contact'));

    }

    /**
     * @inheritDoc
     */
    public function formatMessage($model)
    {
        list($model['when'], $model['date']) = DateHelper::formatMessageDate($model['created_at']);
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function formatConversation($model)
    {
        $model['date'] = DateHelper::formatConversationDate($model['created_at']);
        $model['text'] = StringHelper::truncate($model['text'], 20);
        $model['new_messages'] = ArrayHelper::getValue($model, 'newMessages.count', 0);
        $model['contact'] = ArrayHelper::merge($model['contact'], $model['contact']['profile']);
        $model['deleteUrl'] = Url::to(['delete-conversation','contactId' => $model['contact']['id']]);
        $model['readUrl'] = Url::to(['mark-conversation-as-read','contactId' => $model['contact']['id']]);
        $model['unreadUrl'] = Url::to(['mark-conversation-as-unread','contactId' => $model['contact']['id']]);
        ArrayHelper::remove($model, 'contact.profile');
        ArrayHelper::remove($model, 'newMessages');
        return $model;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return Message::className();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if(null === $this->_user){
            $this->_user = User::findIdentity(\Yii::$app->session->get($this->module->id . '_user', 1));
        }
        return $this->_user;
    }

    public function setUser($userId)
    {
        \Yii::$app->session->set($this->module->id . '_user', $userId);
    }

}
