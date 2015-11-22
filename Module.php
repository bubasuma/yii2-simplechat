<?php

namespace bubasuma\simplechat;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\Instance;


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
 *     text VARCHAR(1000) NOT NULL
 *     is_new BOOLEAN DEFAULT 1,
 *     is_deleted_by_sender BOOLEAN DEFAULT 0,
 *     is_deleted_by_receiver BOOLEAN DEFAULT 0,
 *     created_at DATETIME NOT NULL,
 *     CONSTRAINT fk_message_sender_id FOREIGN KEY (id)
 *         REFERENCES user (id) ON DELETE NO ACTION ON UPDATE CASCADE,
 *     CONSTRAINT fk_message_receiver_id FOREIGN KEY (id)
 *         REFERENCES user (id) ON DELETE NO ACTION ON UPDATE CASCADE,
 * );
 * ~~~
 *
 * The `user` table stores users, and the `message` table stores messages
 * The name of these two tables can be customized by setting [[userTable]]
 * and [[messageTable]], respectively.
 *
 * @author Buba <buba@bigdropinc.com>
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
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                'GET messages/<contactId:\d+>' => $this->id . '/default/index',
                'POST messages/<contactId:\d+>' => $this->id . '/default/messages',
                'DELETE messages/<contactId:\d+>' => $this->id . '/default/delete-conversation',

                'GET messages' => $this->id . '/default/index',
                'POST messages' => $this->id . '/default/conversations',
                'PUT,POST message/<contactId:\d+>' => $this->id . '/default/create-message',
                'DELETE message/<id:\d+>' => $this->id . '/default/delete-message',
            ], false);
        }elseif ($app instanceof \yii\console\Application) {
            $app->controllerMap[$this->id] = [
                'class' => 'bubasuma\simplechat\controllers\ConsoleController',
                'module' => $this,
            ];
        }
    }

}
