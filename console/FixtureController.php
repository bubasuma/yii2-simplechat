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
        if(null === $this->fixtureDataPath){
            $this->fixtureDataPath = "@runtime/{$this->module->id}";
        }
        \Yii::$container->set(UserFixture::classname(), [
            'dataFile' => Yii::getAlias("$this->fixtureDataPath/user.php")
        ]);
        \Yii::$container->set(UserProfileFixture::classname(), [
            'dataFile' => Yii::getAlias("$this->fixtureDataPath/profile.php")
        ]);
        \Yii::$container->set(MessageFixture::classname(), [
            'dataFile' => Yii::getAlias("$this->fixtureDataPath/message.php")
        ]);
    }

    public function actionLoadAll()
    {
        $this->run('load', ['*']);
    }
}