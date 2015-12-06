<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\db\demo\User;
use bubasuma\simplechat\helpers\DateHelper;
use bubasuma\simplechat\Module;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * Class DemoController
 * @package bubasuma\simplechat\controllers
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class DemoController extends DefaultController
{
    /**
     * @var Module
     */
    public $module;

    public $modelClass = 'bubasuma\simplechat\db\demo\Message';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->module->initDemo();
        $this->user = User::findOne(['id' => \Yii::$app->request->get('userId')]);
    }


    public function actionIndex($contactId)
    {
        /**
         * @var $user User
         * @var $contact User
         */
        $user = $this->user;
        $contact = User::findOne(['id' => $contactId]);
        if (empty($contact)) {
            throw new NotFoundHttpException();
        }

        $this->view->title = $contact->fullName;

        $conversationDataProvider = call_user_func([$this->modelClass, 'loadConversations'],
            $user->id, [$this, 'formatConversation'], 8);


        $messageDataProvider = call_user_func([$this->modelClass, 'loadMessages'],
            $user->id, $contact->id, [$this, 'formatMessage'], 10);

        $users = [];

        foreach (User::find()->with('profile')->all() as $userItem) {
            $users[] = [
                'label' => $userItem->fullName,
                'url' => '/messages?userId=' . $userItem->id . '&contactId=' . $contact->id,
                'options' => ['class' => $userItem->id == $contact->id || $userItem->id == $user->id ? 'disabled' : '']
            ];
        }

        return $this->render('index', compact('conversationDataProvider', 'messageDataProvider', 'users', 'user', 'contact'));

    }

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
        $model = parent::formatConversation($model);
        $model['date'] = DateHelper::formatConversationDate($model['created_at']);
        $model['text'] = StringHelper::truncate($model['text'], 20);
        return $model;
    }


}
