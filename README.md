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
"bubasuma/yii2-simplechat": "~2.0.0"
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

# show help information about Simple Chat
yii simplechat

# Apply migration for demo chat by running the following command:
yii simplechat/start

# You can clear your database from demo data and tables by running the following command:
yii simplechat/stop
```

You can then access Simple Chat through the following URL:

```
http://localhost/path/to/index.php?r=messages/2
```

or if you have enabled pretty URLs, you may use the following URL:

```
http://localhost/path/to/index.php/messages2
```

You should see the below:

![yii simple chat demo page](http://i.imgur.com/mB0CsET.png "yii simple chat demo page")

If not, please check if demo migration has been successfully applied against your database. You can check it by running the following command:

```
yii simplechat/start
```
>Note: the command above is accessible only if you have configured your console application as it is recommended above.

##Usage

Create an ActiveRecord like follow:

```php
namespace common\models;

//...
use bubasuma\simplechat\db\Model;
use common\models\User;
use yii\db\ActiveQuery;
//...

class Message extends Model
{
    public function getContact()
    {
        return $this->hasOne(User::className(), ['id' => 'contact_id']);
    }
    
    /**
     * @inheritDoc
     */
    public static function conversations($userId)
    {
        return parent::conversations($userId)->with([
            //...
            'contact' => function ($contact) {
                /**@var $contact ActiveQuery * */
                $contact->with([
                    //...
                ])->select(['id', ]);
            },
            //...
        ]);
    }
}
```

Create a controller like follow:

```php
namespace frontend\controllers;

//...
use yii\web\Controller;
use yii\helpers\StringHelper;
use common\models\Message;
use bubasuma\simplechat\controllers\ControllerTrait;
//...

class MessageController extends Controller
{
    use ControllerTrait;
    
    /**
     * @return string
     */
    public function getModelClass()
    {
        return Message::className();
    }
    
    /**
     * @inheritDoc
     */
    public function formatMessage($model)
    {
        //...
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function formatConversation($model)
    {
        //...
        $model['text'] = StringHelper::truncate($model['text'], 20);
        //...
        return $model;
    }
}
```
>Note: If you are using this extension in your frontend application, you can find the usage of widgets  in `index.twig`.

##FAQ
**Does this extension work with any template engines other than `twig`?**

Yes. Given that, the default render in `yii2` is `php`, you must indicate explicitly the extension part in view names.

**Can I use this extension in a RESTful APIs**

Yes, You can. 

**Can I use different template engines for rendering in server side and client side?**

Yes. But using the same template in both sides remains the best implementation.

 