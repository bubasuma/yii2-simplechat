<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\Instance;
use yii\web\Application as Web;
use yii\console\Application as Console;

/**
 * Module extends [[\yii\base\Module]] and represents a message system that stores
 * messages in database.
 *
 * The database must contain at less the following two tables:
 *
 * ~~~
 *
 * CREATE TABLE user (
 *     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     .. ..
 * );
 *
 * CREATE TABLE message (
 *     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     sender_id BIGINT UNSIGNED NOT NULL,
 *     receiver_id BIGINT UNSIGNED NOT NULL,
 *     text VARCHAR(1020) NOT NULL
 *     is_new BOOLEAN DEFAULT TRUE,
 *     is_deleted_by_sender BOOLEAN DEFAULT FALSE,
 *     is_deleted_by_receiver BOOLEAN DEFAULT FALSE,
 *     created_at DATETIME NOT NULL,
 *     CONSTRAINT fk_message_sender_id FOREIGN KEY (id)
 *         REFERENCES user (id) ON DELETE NO ACTION ON UPDATE CASCADE,
 *     CONSTRAINT fk_message_receiver_id FOREIGN KEY (id)
 *         REFERENCES user (id) ON DELETE NO ACTION ON UPDATE CASCADE,
 * );
 * ~~~
 *
 * The `user` table stores users, and the `message` table stores messages
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     */
    public $db = 'db';

    public $controllerNamespace = 'bubasuma\simplechat\controllers';

    /**
     * Initializes simplechat module.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof Web) {
            $app->getUrlManager()->addRules([
                'messages/<contactId:\d+>' => $this->id . '/default/index',
                'messages' => $this->id . '/default/index',
                'login-as/<userId:\d+>' => $this->id . '/default/login-as',
                'chat/get/messages/<contactId:\d+>' => $this->id . '/default/messages',
                'chat/get/conversations' => $this->id . '/default/conversations',
                'chat/delete/message/<id:\d+>' => $this->id . '/default/delete-message',
                'chat/delete/conversation/<contactId:\d+>' => $this->id . '/default/delete-conversation',
                'chat/post/message/<contactId:\d+>' => $this->id . '/default/create-message',
                'chat/unread/conversation/<contactId:\d+>' => $this->id . '/default/mark-conversation-as-unread',
                'chat/read/conversation/<contactId:\d+>' => $this->id . '/default/mark-conversation-as-read',
            ], false);
            if (!isset($app->getView()->renderers['twig'])) {
                $app->getView()->renderers['twig'] = [
                    'class' => 'yii\twig\ViewRenderer',
                ];
            }
            $app->getView()->renderers['twig']['globals']['html'] = '\yii\helpers\Html';
        } elseif ($app instanceof Console) {
            $app->controllerMap[$this->id] = [
                'class' => 'bubasuma\simplechat\console\DefaultController',
                'module' => $this,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->db->tablePrefix = $this->id . '_';
        return true;
    }
}
