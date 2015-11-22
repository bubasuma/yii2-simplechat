<?php

namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: buba
 * Date: 18.10.15
 * Time: 10:00
 */
class MessageAsset extends AssetBundle
{
    public static $live = false;
    public $sourcePath = '@frontend/modules/simplechat/assets';
    public $js = [
        'js/messages.js',
    ];

    public function init()
    {
        parent::init();
        if(self::$live){
            array_unshift($this->js,'//cdn.socket.io/socket.io-1.3.5.js');
        }
    }
}