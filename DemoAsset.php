<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * Class DemoAsset
 * @package bubasuma\simplechat
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
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
        'bubasuma\simplechat\BaseAsset',
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . '/assets';
    }
}
