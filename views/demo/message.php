<?php
use bubasuma\simplechat\db\demo\User;
use bubasuma\simplechat\MessageWidget;

/**
 * @var $model array
 * @var $user User
 * @var $contact User
 * @var $widget MessageWidget
 */

$sender = $model['sender_id'] == $user['id'] ? $user : $contact;
?>

<a class="pull-left" href="#">
    <img class="media-object" data-src="holder.js/64x64" alt="64x64" style="width: 32px; height: 32px;"
         src="<?= $widget->clientOptions['baseUrl'] ?>/img/<?= $sender['profile']['avatar'] ?>">
</a>
<div class="media-body">
    <small class="pull-right time"><i class="fa fa-clock-o"></i> <?= $model['date'] ?></small>
    <h5 class="media-heading"><?= $sender['profile']['full_name'] ?></h5>
    <small class="col-lg-10"><?= $model['text'] ?></small>
</div>
