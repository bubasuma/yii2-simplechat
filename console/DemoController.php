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
 * Class DemoController
 * @package bubasuma\simplechat\console
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class DemoController extends Controller
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
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $migrationPath = '@bubasuma/simplechat/migrations';

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!strcmp($action->id, 'start') || !strcmp($action->id, 'stop')) {
                $this->module->initDemo();
                $this->module->controllerMap['migrate'] = [
                    'class' => 'bubasuma\simplechat\console\MigrateController',
                    'migrationPath' => $this->migrationPath
                ];
            }
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
        $this->run("migrate/up");
    }

    /**
     * Clear database from demo data and tables
     */
    public function actionStop()
    {
        $this->run("migrate/down");
    }

    /**
     * @inheritdoc
     */
    public function getUniqueID()
    {
        return $this->id;
    }
}