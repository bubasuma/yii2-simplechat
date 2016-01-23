<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\web\AssetBundle;

/**
 * Class TwigAsset
 * @package bubasuma\simplechat
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
 */
class TwigAsset extends AssetBundle
{
    public $sourcePath = '@bower/twig.js';
    public $js = [
        'twig.js',
    ];
}
