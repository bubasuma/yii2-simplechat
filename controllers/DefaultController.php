<?php

namespace frontend\modules\simplechat\controllers;

use frontend\modules\simplechat\DataProvider;
use frontend\modules\simplechat\db\Model;
use frontend\modules\simplechat\db\ConversationQuery;
use frontend\modules\simplechat\db\MessageQuery;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    public $modelClass = 'frontend\modules\simplechat\db\ChatModel';
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
                    'delete-message',
                    'conversations',
                    'delete-conversation',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'index'  => ['get'],
                    'messages'   => ['post'],
                    'conversations' => ['post'],
                    'create-message' => ['post', 'put'],
                    'delete-message' => ['delete'],
                    'delete-conversation' => ['delete'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        throw new NotSupportedException();
    }

    public function actionConversations()
    {
        $userId = $this->user->id;
        $callable = [$this->modelClass, 'loadConversations'];
        $formatter = [$this, 'formatConversation'];
        $limit = \Yii::$app->request->post('limit');
        return  call_user_func($callable, $userId, $formatter, $limit);
    }

    public function actionMessages()
    {
        $userId = $this->user->id;
        $contactId = \Yii::$app->request->get('contactId');
        $callable = [$this->modelClass, 'loadMessages'];
        $formatter = [$this, 'formatMessage'];
        $limit = \Yii::$app->request->post('limit');
        return call_user_func($callable, $userId, $contactId, $formatter, $limit);
    }

    public function actionCreateMessage(){
        $userId = $this->user->id;
        $contactId = \Yii::$app->request->get('contactId');
        $text = \Yii::$app->request->post('text');
        return call_user_func([$this->modelClass,'create'], $userId, $contactId, $text);
    }

    public function actionDeleteMessage(){
        throw new NotSupportedException(get_class($this) . ' does not support actionDeleteMessage().');
    }

    public function actionDeleteConversation(){
        throw new NotSupportedException(get_class($this) . ' does not support actionDeleteConversation().');
    }

    /**
     * @param array|Model $model
     * @return array
     */
    public function formatMessage($model){
        return $model;
    }

    /**
     * @param array $model
     * @return array
     */
    public function formatConversation($model){
        return $model;
    }

    /**
     * Returns the directory containing view files for this controller.
     * returns the directory named as controller [[id]] under the app's
     * [[viewPath]] directory.
     * @return string the directory containing the view files for this controller.
     */
    public function getViewPath()
    {
        $className = preg_replace('/[A-Za-z_-]+\\\/','',$this->className());
        $folder = str_replace('-controller','', trim(Inflector::camel2id($className),'-'));
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . $folder;
    }

}
