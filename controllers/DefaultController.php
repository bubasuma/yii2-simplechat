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
        if($contactId == $user->id){
            throw new ForbiddenHttpException('You cannot open this conversation');
        }
        $current = new Conversation(['contact_id' => $contactId]);
        $contact = $current->contact;
        if (empty($contact)) {
            throw new NotFoundHttpException();
        }
        $this->view->title = $contact->name;
        /** @var $conversationClass Conversation */
        $conversationClass = $this->conversationClass;
        $conversationDataProvider = $conversationClass::get($user->id, 8);
        /** @var $messageClass Message */
        $messageClass = $this->messageClass;
        $messageDataProvider = $messageClass::get($user->id, $contact->id, 10);
        $users = [];
        foreach (User::getAll() as $userItem) {
            $users[] = [
                'label' => $userItem->name,
                'url' => Url::current(['userId' => $userItem->id]),
                'options' => ['class' => in_array($userItem->id, [$user->id, $contact->id]) ? 'disabled' : ''],
                'linkOptions' => ['data-method' => 'post'],
            ];
        }
        return $this->render('index.twig', compact('conversationDataProvider', 'messageDataProvider', 'users', 'user', 'contact', 'current'));

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
