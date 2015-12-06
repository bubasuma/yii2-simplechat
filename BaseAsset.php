<?php
namespace bubasuma\simplechat;


use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
    public $css = [
        'css/default.css',
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