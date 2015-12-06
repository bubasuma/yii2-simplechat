<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use bubasuma\simplechat\migrations\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m151121_105707_message_data
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class m151121_105707_message_data extends Migration
{
    public function up()
    {
        $users = \bubasuma\simplechat\db\demo\User::find()->select(['id'])->asArray()->all();
        $data = require(__DIR__ . '/data/messages.php');
        $count = count($data);
        $messages = [];
        try {
            for ($i = 1; $i <= 10000; $i++) {
                shuffle($users);
                $sender = $users[0];
                $receiver = $users[1];
                $messages[] = [
                    'sender_id' => $sender['id'],
                    'receiver_id' => $receiver['id'],
                    'created_at' => (new \DateTime())->sub(new \DateInterval('P' . (mt_rand() % 500) . 'DT' . (mt_rand() % 86400) . 'S'))->getTimestamp(),
                    'text' => $data[mt_rand(0, $count - 1)],
                ];
            }
            ArrayHelper::multisort($messages, 'created_at', SORT_ASC, SORT_NUMERIC);
            foreach ($messages as $message) {
                $new = new \bubasuma\simplechat\db\demo\Message();
                $new->sender_id = $message['sender_id'];
                $new->receiver_id = $message['receiver_id'];
                $new->text = $message['text'];
                $new->is_new = 0;
                $new->created_at = date('Y-m-d H:i:s', $message['created_at']);
                $new->save();
            }
            return true;
        } catch (\yii\base\Exception $e) {
            return false;
        }

    }

    public function down()
    {
        $this->truncateTable(self::TABLE_MESSAGE);
    }
}
