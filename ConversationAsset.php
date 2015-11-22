<?php

namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: buba
 * Date: 18.10.15
 * Time: 10:00
 */
class ConversationAsset extends AssetBundle
{
    public $js = [
        'js/conversations.js',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/assets';
    }
}