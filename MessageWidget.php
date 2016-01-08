<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ListView;

/**
 * Class MessageWidget
 * @package bubasuma\simplechat
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class MessageWidget extends ListView
{
    /**
     * @var array
     */
    public $user;

    /**
     * @var array
     */
    public $contact;

    public $itemView = 'message';

    public $formView = 'form';

    public $formParams = [];

    public $formOptions = ['method' => 'post'];

    public $clientOptions = [];

    public $liveOptions = [];

    private $tag;


    public function renderForm()
    {
        $action = ArrayHelper::remove($this->formOptions, 'action', '');
        $method = ArrayHelper::remove($this->formOptions, 'method', 'post');

        if (!isset($this->formOptions['id'])) {
            $this->formOptions['id'] = 'msg-form';
        }

        $content = Html::beginForm($action, $method, $this->formOptions);

        if (is_string($this->formView)) {
            $content .= $this->getView()->render($this->formView, array_merge([
                'widget' => $this,
            ], $this->formParams));
        } else {
            $content .= call_user_func($this->formView, $this);
        }

        $content .= Html::endForm();

        return $content;

    }

    public function registerJs()
    {
        $id = $this->options['id'];
        if (!isset($this->clientOptions['selector'])) {
            $class = explode(' ', $this->itemOptions['class']);
            $this->clientOptions['selector'] = '.' . $class[0];
        }
        $this->clientOptions['form'] = '#' . $this->formOptions['id'];
        $options = Json::htmlEncode($this->clientOptions);
        $user = Json::htmlEncode($this->user);
        $contact = Json::htmlEncode($this->contact);
        $view = $this->getView();
        MessageAsset::register($view);
        $view->registerJs("jQuery('#$id').simpleChatMessages($user,$contact,$options);");
    }


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->itemOptions['class'])) {
            $this->itemOptions['class'] = 'msg-item';
        }
        $this->tag = ArrayHelper::remove($this->options, 'tag', 'div');
        echo Html::beginTag($this->tag, $this->options);

    }

    public function run()
    {
        $this->registerJs();
        echo Html::endTag($this->tag);
    }

    public function renderItem($model, $key, $index)
    {
        if ($this->itemView === null) {
            $content = $key;
        } elseif (is_string($this->itemView)) {
            $content = $this->getView()->render($this->itemView, array_merge([
                'model' => $model,
                'key' => $key,
                'index' => $index,
                'widget' => $this,
                'user' => $this->user,
                'contact' => $this->contact,
            ], $this->viewParams));
        } else {
            $content = call_user_func($this->itemView, $model, $key, $index, $this);
        }
        $options = $this->itemOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        if ($tag !== false) {
            $options['data-key'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string)$key;

            return Html::tag($tag, $content, $options);
        } else {
            return $content;
        }
    }

    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach (array_reverse($models, true) as $index => $model) {
            $rows[] = $this->renderItem($model, $keys[$index], $index);
        }

        return implode($this->separator, $rows);
    }


    public function renderSection($name)
    {
        switch ($name) {
            case '{items}':
                return $this->renderItems();
            case '{form}':
                return $this->renderForm();
            default:
                return false;
        }
    }

    public function getId()
    {
        $users = [$this->user['id'], $this->contact['id']];
        sort($users);
        return md5(implode('&', $users));
    }
}