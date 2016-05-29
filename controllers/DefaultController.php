<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\models\Conversation;
use bubasuma\simplechat\models\Message;
use bubasuma\simplechat\models\User;
use bubasuma\simplechat\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
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
                    'index' => ['get'],
                    'login-as' => ['post'],
                    'messages' => ['get'],
                    'conversations' => ['get'],
                    'create-message' => ['post'],
                    'delete-conversation' => ['delete'],
                    'mark-conversation-as-read' => ['patch', 'put'],
                    'mark-conversation-as-unread' => ['patch', 'put'],
                ],
            ]
        ]);
    }

    public function actionIndex($contactId = null)
    {
        $user = $this->user;
        if ($contactId == $user->id) {
            throw new ForbiddenHttpException('You cannot open this conversation');
        }

        if (isset($contactId)) {
            $current = new Conversation(['user_id' => $user->id, 'contact_id' => $contactId]);
        }

        /** @var $conversationClass Conversation */
        $conversationClass = $this->conversationClass;
        $conversationDataProvider = $conversationClass::get($user->id, 8);

        if (!isset($current)) {
            if (0 == $conversationDataProvider->getTotalCount()) {
                throw new NotFoundHttpException('You have no active conversations');
            }
            $current = current($conversationDataProvider->getModels());
        }

        $contact = $current['contact'];
        if (empty($contact)) {
            throw new NotFoundHttpException();
        }
        $this->view->title = $contact['name'];
        /** @var $messageClass Message */
        $messageClass = $this->messageClass;
        $messageDataProvider = $messageClass::get($user->id, $contact['id'], 10);
        $users = $this->getUsers([$user->id, $contact['id']]);
        return $this->render(
            'index.twig',
            compact('conversationDataProvider', 'messageDataProvider', 'users', 'user', 'contact', 'current')
        );

    }

    public function actionLoginAs($userId)
    {
        $this->setUser($userId);
        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function getMessageClass()
    {
        return Message::className();
    }

    /**
     * @return string
     */
    public function getConversationClass()
    {
        return Conversation::className();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (null === $this->_user) {
            $this->_user = User::findIdentity(\Yii::$app->session->get($this->module->id . '_user', 1));
        }
        return $this->_user;
    }

    public function setUser($userId)
    {
        \Yii::$app->session->set($this->module->id . '_user', $userId);
    }

    public function getUsers(array $except = [])
    {
        $users = [];
        foreach (User::getAll() as $userItem) {
            $users[] = [
                'label' => $userItem->name,
                'url' => Url::to(['login-as', 'userId' => $userItem->id]),
                'options' => ['class' => in_array($userItem->id, $except) ? 'disabled' : ''],
                'linkOptions' => ['data-method' => 'post'],
            ];
        }
        return $users;
    }
}
