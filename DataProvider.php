<?php
/**
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace bubasuma\simplechat;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Class DataProvider
 * @package bubasuma\simplechat
 *
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class DataProvider extends ActiveDataProvider implements Arrayable
{
    use ArrayableTrait;

    public $formatter = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        $request = \Yii::$app->request;
        $key = $request->get('key', $request->post('key'));
        if ($key) {
            $type = $request->get('type', $request->post('type'));
            if (!strcmp('history', $type)) {
                $this->query->andWhere(['<', 'id', $key]);
            } else {
                $this->query->andWhere(['>', 'id', $key]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = parent::prepareModels();
        if (null !== $this->formatter) {
            foreach ($models as $index => $model) {
                $models[$index] = call_user_func($this->formatter, $model);
            }
        }
        return $models;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            'totalCount',
            'keys',
            'models',
        ];
    }


}