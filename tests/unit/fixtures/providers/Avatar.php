<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\tests\unit\fixtures\providers;

use Faker\Provider\Base;
use yii\helpers\FileHelper;

class Avatar extends Base
{
    private $_container;

    public function avatar()
    {
        $path = __DIR__ . '/../../../../assets/img/avatars';
        if (null === $this->_container) {
            $this->_container = array_map(
                function ($file) {
                    return basename($file);
                },
                FileHelper::findFiles($path)
            );
        }
        return $this->generator->randomElement($this->_container);
    }
}
