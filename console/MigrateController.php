<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat\console;

use yii\db\Query;
use yii\helpers\Console;

/**
 * Class MigrateController
 * @package bubasuma\simplechat\console
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $migrationPath = '@bubasuma/simplechat/migrations';

    /**
     * @inheritDoc
     */
    public function actionDown($limit = 'all')
    {
        $ret = parent::actionDown('all');
        $query = new Query;
        $query->from($this->migrationTable);
        if (1 == $query->count()) {
            $tableName = $this->db->schema->getRawTableName($this->migrationTable);
            $this->stdout("Deleting migration history table \"$tableName\"...", Console::FG_YELLOW);
            $this->db->createCommand()->dropTable($this->migrationTable)->execute();
            $this->stdout("Done.\n", Console::FG_GREEN);
        }
        return $ret;
    }
}
