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
    /**
     * @var Module
     */
    public $module;

    /**
     * @var string the default command action.
     */
    public $defaultAction = 'index';

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
     * Apply migration for demo chat by creating tables and data
     */
    public function actionStart()
    {
        $this->run('migrate/up');
    }

    /**
     * Clear database from demo data and tables
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
        $this->run('fixture/load-all');
    }

    /**
     * @since 2.0
     */
    protected function generateFixtures()
    {
        $this->run('fixture/generate-all');
    }

    /**
     * @inheritdoc
     */
    public function getUniqueID()
    {
        return $this->id;
    }
}