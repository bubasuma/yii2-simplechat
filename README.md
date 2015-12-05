Yii2 Simple Chat
================
A simple chat for your yii2 application

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bubasuma/yii2-simplechat "dev-master"
```

or add

```
"bubasuma/yii2-simplechat": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

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
>Note:Use the save configuration for your console application.

You can access Simple Chat via command line as follows,

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


