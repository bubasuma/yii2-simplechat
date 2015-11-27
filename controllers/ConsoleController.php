<?php
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\Module;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Yii2 SimpleChat Demo
 * Creates or clears demo data and tables
 * @package bubasuma\simplechat\controllers
 */
class ConsoleController extends Controller
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
            $path = \Yii::getAlias($this->migrationPath);
            $this->migrationPath = $path;
            $this->module->db->tablePrefix = $this->module->id.'_';
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
        $this->module->runAction("migrate",['migrationPath' => $this->migrationPath]);
    }

    /**
     * Clear database from demo data and tables
     */
    public function actionStop()
    {
        $this->module->runAction("migrate/down",['migrationPath' => $this->migrationPath]);
    }

    /**
     * @inheritdoc
     */
    public function getUniqueID()
    {
        return $this->id;
    }
}