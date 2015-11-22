<?php

namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\db\demo\User;
use bubasuma\simplechat\helpers\DateHelper;
use bubasuma\simplechat\Module;
use yii\web\NotFoundHttpException;

class DemoController extends DefaultController
{
    /**
     * @var Module
     */
    public $module;

    public $layout = 'main';
    public $modelClass = 'bubasuma\simplechat\db\demo\Message';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->module->db->tablePrefix = $this->module->id.'_';
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
        if(empty($contact)){
            throw new NotFoundHttpException();
        }

        $this->view->title = $contact->fullName;

        $conversationDataProvider = call_user_func([$this->modelClass, 'loadConversations'],
            $user->id, [$this, 'formatConversation'], 8);

        $messageDataProvider = call_user_func([$this->modelClass, 'loadMessages'],
            $user->id, $contact->id, [$this, 'formatMessage'], 10);

        return $this->render('index',compact('conversationDataProvider','messageDataProvider','user','contact'));

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
        $model['date'] = DateHelper::formatConversationDate($model['created_at']);
        $model['text'] = \yii\helpers\StringHelper::truncate($model['text'],30);
        $model['load_url'] = '/messages/' . $model['contact']['id'] . '?userId='.$this->user->id;
        $model['send_url'] = '/message/' . $model['contact']['id']. '?userId='.$this->user->id;
        return $model;
    }


}
