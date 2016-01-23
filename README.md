Yii2 Simple Chat
================
A simple chat for your yii2 application

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bubasuma/yii2-simplechat
```

or add

```
"bubasuma/yii2-simplechat": "~1.0.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => ['simplechat'],
    'modules' => [
        'simplechat' => [
            'class' => 'bubasuma\simplechat\Module',
            'controllerMap' => [
                //demo configuration. Replace 'bubasuma\simplechat\controllers\DemoController'
                //to your controller class path
                'default' => 'bubasuma\simplechat\controllers\DemoController'
            ],
        ],
        // ...
    ],
    // ...
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [                    
                    //Demo configuration. Replace @bubasuma/simplechat/views/demo 
                    //to your view path
                    '@bubasuma/simplechat/views/default' => '@bubasuma/simplechat/views/demo',
                ],
            ],
        ],
        // ...
    ],
    // ...
];
```
Use this configuration for your console application:

```php
return [
    'bootstrap' => ['simplechat'],
    'modules' => [
        'simplechat' => 'bubasuma\simplechat\Module',
        // ...
    ],
    // ...
];
```
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
http://localhost/path/to/index.php?r=messages?userId=1&contactId=2
```

or if you have enabled pretty URLs, you may use the following URL:

```
http://localhost/path/to/index.php/messages?userId=1&contactId=2
```

You should see the below "Congratulations!" page in your browser, if you are running extension without demo configuration:

![yii simple chat Congratulation page](http://i.imgur.com/lLQnfHs.png "yii simple chat Congratulation page")

If you are running with demo configurations:

![yii simple chat demo page](http://i.imgur.com/mB0CsET.png "yii simple chat demo page")

If not, please check if demo migration has been successfully applied against your database. You can check it by running the following command:

```
yii simplechat/start
```
>Note: the command above is accessible only if you have configured your console application as it is recommended above.