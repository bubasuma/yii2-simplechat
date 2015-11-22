<?php
namespace bubasuma\simplechat\controllers;

use bubasuma\simplechat\Module;
use yii\console\controllers\MigrateController;
use yii\db\Query;
use yii\helpers\Console;


class ConsoleController extends MigrateController
{
    /**
     * @var Module
     */
    public $module;

    /**
     * @var string the default command action.
     */
    public $defaultAction = 'start';
    /**
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $migrationPath = '@vendor/bubasuma/simplechat/migrations';

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if ($action->id == 'create') {
                throw new \yii\base\NotSupportedException();
            }else{
                $this->module->db->tablePrefix = $this->module->id.'_';
            }
            return true;
        } else {
            return false;
        }
    }

    public function actionStart()
    {
        $this->actionUp();
    }

    public function actionStop()
    {
        $this->actionDown('all');
        $query = new Query;
        $query->from($this->migrationTable);
        if(1 == $query->count()){
            $this->deleteMigrationHistoryTable();
        }

    }

    /**
     * Creates the migration history table.
     */
    protected function deleteMigrationHistoryTable()
    {
        $tableName = $this->db->schema->getRawTableName($this->migrationTable);
        $this->stdout("Deleting migration history table \"$tableName\"...", Console::FG_YELLOW);
        $this->db->createCommand()->dropTable($this->migrationTable)->execute();
        $this->stdout("Done.\n", Console::FG_GREEN);
    }
}