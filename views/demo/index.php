<?php

use bubasuma\simplechat\db\demo\User;
use bubasuma\simplechat\DemoAsset;

/**
 * @var $user User
 * @var $contact User
 * @var array $users
 * @var $messageDataProvider \yii\data\ActiveDataProvider
 * @var $conversationDataProvider \yii\data\ActiveDataProvider
 * @var $this \yii\web\View
 */
$asset = DemoAsset::register($this);
$this->params['user'] = $user;
$this->params['users'] = $users;

?>
<div class="row">
    <div class="loading" style="display: none">Loading&#8230;</div>
    <?php

    $conversation = \bubasuma\simplechat\ConversationWidget::begin([
        'dataProvider' => $conversationDataProvider,
        'options' => ['class' => 'conversation-wrap col-lg-3', 'id' => 'conv-wrap'],
        'itemOptions' => ['class' => 'media conversation'],
        'user' => [
            'id' => $user->id,
            'profile' => [
                'full_name' => $user->fullName
            ],
        ],
        'clientOptions' => [
            'loadUrl' => '/conversations?userId=' . $user->id,
            'deleteUrl' => '/conversation?userId=' . $user->id,
            'readUrl' => '/conversation/read?userId=' . $user->id,
            'unreadUrl' => '/conversation/unread?userId=' . $user->id,
            'template' => '#conv-tmpl',
            'currentCssClass' => 'current',
            'unreadCssClass' => 'unread',
            'baseUrl' => $asset->baseUrl
        ]
    ]);
    ?>

    <?php echo $conversation->renderItems() ?>
    <div id="conv-loader" style="display: none" class="text-center">
        <img alt="Loading..." src="<?= $asset->baseUrl ?>/img/inf-square-loader.gif"/>
    </div>
    <?php
    bubasuma\simplechat\ConversationWidget::end();
    ?>

    <?php
    $message = \bubasuma\simplechat\MessageWidget::begin([
        'dataProvider' => $messageDataProvider,
        'user' => [
            'id' => $user->id,
            'profile' => [
                'full_name' => $user->fullName,
                'avatar' => $user->profile->avatar,
            ],
        ],
        'contact' => [
            'id' => $contact->id,
            'profile' => [
                'full_name' => $contact->fullName,
                'avatar' => $contact->profile->avatar,
            ],
        ],
        'options' => ['class' => 'message-wrap col-lg-8', 'id' => 'messages'],
        'itemOptions' => ['class' => 'media msg'],
        'formOptions' => [
            'action' => '/message?userId=' . $user->id,
            'method' => 'post'
        ],
        'clientOptions' => [
            'loadUrl' => '/messages?userId=' . $user->id,
            'container' => '#msg-wrap',
            'template' => '#msg-tmpl',
            'baseUrl' => $asset->baseUrl,
        ]
    ]);
    ?>
    <div id="msg-wrap" class="msg-wrap">
        <div id="msg-loader" style="display: none" class="text-center">
            <img alt="Loading..." src="<?= $asset->baseUrl ?>/img/inf-circle-loader.gif"/>
        </div>
        <?php //echo $message->renderItems();?>
        <?php
        $models = $message->dataProvider->getModels();
        $keys = $message->dataProvider->getKeys();
        $rows = [];
        $when = false;
        foreach (array_reverse($models, true) as $index => $model) {
            if (strcmp($when, $model['when'])) {
                $when = $model['when'];
                $rows[] = \yii\bootstrap\Html::tag('div', "<strong>$when</strong>", ['class' => 'alert alert-info msg-date']);
            }
            $rows[] = $message->renderItem($model, $keys[$index], $index);
        }
        echo implode($message->separator, $rows);
        ?>
    </div>

    <div class="send-wrap ">
        <?= $message->renderForm(); ?>
    </div>
    <div class="btn-panel">
        <!--<a href="" class=" col-lg-3 btn   send-message-btn " role="button"><i class="fa fa-cloud-upload"></i> Add Files</a>-->
        <a id="msg-send" href="" class=" col-lg-4 text-right btn   send-message-btn pull-right" role="button"><i
                class="fa fa-location-arrow"></i> Send Message</a>
    </div>
    <?php
    bubasuma\simplechat\MessageWidget::end();
    ?>
</div>
<?php require 'conversation.html' ?>
<?php require 'message.html' ?>


