<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bubasuma\simplechat\console;

use bubasuma\simplechat\tests\unit\fixtures\MessageFixture;
use bubasuma\simplechat\tests\unit\fixtures\UserFixture;
use bubasuma\simplechat\tests\unit\fixtures\UserProfileFixture;
use Yii;

/**
 * Yii2 SimpleChat Demo
 * Creates fixtures
 *
 * Class FixtureController
 * @package bubasuma\simplechat\console
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 2.0
 */
class FixtureController extends \yii\faker\FixtureController
{
    /**
     * @var array Additional data providers that can be created by user and will be added to the Faker generator.
     * More info in [Faker](https://github.com/fzaninotto/Faker.) library docs.
     */
    public $providers = [
        'bubasuma\simplechat\tests\unit\fixtures\providers\Avatar',
    ];
    /**
     * @var string default namespace to search fixtures in
     */
    public $namespace = 'bubasuma\simplechat\tests\unit\fixtures';
    /**
     * @var string Alias to the template path, where all tables templates are stored.
     */
    public $templatePath = '@bubasuma/simplechat/tests/unit/fixtures/templates';
    /**
     * @var string Alias to the fixture data path, where data files should be written.
     */
    public $fixtureDataPath = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (null === $this->fixtureDataPath) {
            $this->fixtureDataPath = "@runtime/{$this->module->id}";
        }
        \Yii::$container
            ->set(UserFixture::classname(), ['dataFile' => Yii::getAlias("$this->fixtureDataPath/user.php")])
            ->set(UserProfileFixture::classname(), ['dataFile' => Yii::getAlias("$this->fixtureDataPath/profile.php")])
            ->set(MessageFixture::classname(), ['dataFile' => Yii::getAlias("$this->fixtureDataPath/message.php")]);
    }
}
