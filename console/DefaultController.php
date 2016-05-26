<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\console;

use bubasuma\simplechat\Module;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Yii2 SimpleChat Demo
 * Creates or clears demo data and tables
 *
 * Class DefaultController
 * @package bubasuma\simplechat\console
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class DefaultController extends Controller
{
    public $interactive = false;
    /**
     * @var Module
     */
    public $module;

    /**
     * @var string the default command action.
     */
    public $defaultAction = 'index';

    /**
     * @var int the count of users to generate
     */
    public $users = 20;

    /**
     * @var int the count of messages to generate
     */
    public $messages = 1000;

    /**
     * @var string Language to use when generating fixtures data.
     */
    public $language;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'users', 'messages', 'language'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->module->controllerMap['migrate'] = [
                'class' => MigrateController::className(),
                'interactive' => $this->interactive,
            ];
            $this->module->controllerMap['fixture'] = [
                'class' => FixtureController::className(),
                'interactive' => $this->interactive,
                'language' => $this->language,
            ];
            $this->stdout("Yii2 SimpleChat Demo\n\n", Console::BOLD);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Shows help
     */
    public function actionIndex()
    {
        $this->run('/help', ['simplechat']);
    }

    /**
     * Create test tables, generates and load fixtures
     */
    public function actionStart()
    {
        $this->run('migrate/up');
        $this->generateFixtures();
        $this->loadFixtures();
    }

    /**
     * Unload fixtures
     * @since 2.0
     */
    public function actionClean()
    {
        $this->unloadFixtures();
    }

    /**
     * Unload fixtures and load them again
     * @since 2.0
     */
    public function actionReset()
    {
        $this->unloadFixtures();
        $this->generateFixtures();
        $this->loadFixtures();
    }

    /**
     * Unload fixtures and drop test tables
     */
    public function actionStop()
    {
        $this->run('migrate/down');
    }

    /**
     * @since 2.0
     */
    protected function loadFixtures()
    {
        $this->run('fixture/load', ['*']);
    }

    /**
     * @since 2.0
     */
    protected function unloadFixtures()
    {
        $this->run('fixture/unload', ['*']);
    }

    /**
     * @since 2.0
     */
    protected function generateFixtures()
    {
        $this->run('fixture/generate', ['user', 'profile','count' => $this->users]);
        $this->run('fixture/generate', ['message','count' => $this->messages]);
    }

    /**
     * @inheritdoc
     */
    public function getUniqueID()
    {
        return $this->id;
    }
}
