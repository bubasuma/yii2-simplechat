<?php
/**
 * Created by PhpStorm.
 * User: buba
 * Date: 27.11.15
 * Time: 12:20
 */

namespace bubasuma\simplechat\console;

use yii\db\Query;
use yii\helpers\Console;

class MigrateController extends \yii\console\controllers\MigrateController
{

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

    /**
     * @inheritDoc
     */
    public function actionDown($limit = 'all')
    {
        $ret =  parent::actionDown('all');
        $query = new Query;
        $query->from($this->migrationTable);
        if(1 == $query->count()){
            $this->deleteMigrationHistoryTable();
        }
        return $ret;
    }


}