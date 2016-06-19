#Yii2 Simple Chat
A simple chat for your yii2 application

##Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bubasuma/yii2-simplechat
```

or add

```
"bubasuma/yii2-simplechat": "*"
```

to the require section of your `composer.json` file.

##Demo

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => ['simplechat'],
    'modules' => [
        'simplechat' => [
            'class' => 'bubasuma\simplechat\Module',
        ],
        // ...
    ],
    // ...
];
```
Use the same configuration for your console application:

>Note: You need this configuration to access simple chat via command line. You can remove it in production mode.

You can access Simple Chat via command line as follows:

```
# change path to your application's base path
cd path/to/AppBasePath

# show available commands
php yii simplechat

# create test tables, generates and load fixtures
php yii simplechat/start

# unload fixtures
php yii simplechat/clean

# unload fixtures and load them again
php yii simplechat/reset

# unload fixtures and drop test tables
php yii simplechat/stop
```

You can specify different options of the `start` and `reset` command:

```
# You can specify how many fixtures per user and message you need by the --users and --messages options
php yii simplechat/start --users=50 --messages=10000
php yii simplechat/reset --users=20 --messages=5000

# You can specify in what language to generate fixtures by the --language option. Thanks to yii2-faker
php yii simplechat/start --language="ru_RU"
php yii simplechat/reset --language="fr_FR"

```

You can then access Simple Chat through the following URL:

```
http://localhost/path/to/index.php?r=messages
```

or if you have enabled pretty URLs, you may use the following URL:

```
http://localhost/path/to/index.php/messages
```

You should see the below:

![yii simple chat demo page](http://i.imgur.com/1YZdjN8.png "yii simple chat demo page")

If not, please check if demo migration has been successfully applied against your database. You can check it by running the following command:

```
php yii simplechat/start
```
>Note: the command above is accessible only if you have configured your console application as it is recommended above.

##Usage

Extend the main `conversation` class like follow:

```php
namespace common\models;

use common\models\User;
//...

class Conversation extends \bubasuma\simplechat\db\Conversation
{
    public function getContact()
    {
        return $this->hasOne(User::className(), ['id' => 'contact_id']);
    }
    
    /**
     * @inheritDoc
     */
    protected static function baseQuery($userId)
    {
        return parent::baseQuery($userId) ->with(['contact.profile']);
    }
    
    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            //...
            'contact' => function ($model) {
                return $model['contact'];
            },
            'deleteUrl',
            'readUrl',
            'unreadUrl',
            //...
        ];
    }
}
```

Extend the main `message` class like follow:

```php
namespace common\models;

//...

class Message extends \bubasuma\simplechat\db\Message
{
    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            //...
            'text',
            'date' => 'created_at',
            //...
        ];
    }
}
```

Create a controller like follow:

```php
namespace frontend\controllers;

//...
use yii\web\Controller;
use common\models\Conversation;
use common\models\Message;
use bubasuma\simplechat\controllers\ControllerTrait;
//...

class MessageController extends Controller
{
    use ControllerTrait;
    
    /**
     * @return string
     */
    public function getMessageClass()
    {
        return Message::className();
    }

    /**
     * @return string
     */
    public function getConversationClass()
    {
        return Conversation::className();
    }
}
```
>Note: If you are using this extension in your frontend application, you can find the usage of widgets  in `index.twig`.

##FAQ
**Does this extension work with any template engines other than `twig`?**

Yes. Given that, the default render in `yii2` is `php`, you must indicate explicitly the extension part in view names.

**Can I use this extension in a RESTful APIs?**

Yes, You can. 

**Can I use different template engines for rendering in server side and client side?**

Yes. But using the same template in both sides remains the best implementation.

 