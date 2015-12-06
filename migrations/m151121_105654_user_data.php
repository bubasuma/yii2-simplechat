<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use bubasuma\simplechat\migrations\Migration;

/**
 * Class m151121_105654_user_data
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class m151121_105654_user_data extends Migration
{
    public function up()
    {
        $data = require(__DIR__ . '/data/users.php');
        try {
            foreach ($data as $item) {
                $user = new \bubasuma\simplechat\db\demo\User();
                $user->setAttributes($item);
                if ($user->save()) {
                    $profile = new \bubasuma\simplechat\db\demo\UserProfile();
                    $profile->setAttributes($item);
                    $profile->id = $user->id;
                    if (!$profile->save()) {
                        $user->delete();
                    }
                }
            }
            return true;

        } catch (\yii\base\Exception $e) {
            return false;
        }

    }

    public function down()
    {
        $this->delete(self::TABLE_USER);
    }
}
