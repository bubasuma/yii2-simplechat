<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * Class ChatAsset
 * @package bubasuma\simplechat
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class ChatAsset extends AssetBundle
{
    public $css = [
        '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css',
        'css/yiiSimpleChat.css',
    ];
    public $js = [
        'js/yiiSimpleChat.js'
    ];
    public $depends = [
        'bubasuma\simplechat\BaseAsset',
        'bubasuma\simplechat\TwigAsset',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/assets';
    }
}
