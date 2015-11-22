<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DemoAsset extends AssetBundle
{
    public $css = [
        '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css',
        'css/chat.css',
    ];
    public $js = [
        'js/chat.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init(){
        parent::init();
        $this->sourcePath = __DIR__ . '/assets';
    }
}
