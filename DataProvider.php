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
        if ($request->post('key')) {
            if (!strcmp('history', $request->post('type'))) {
                $this->query->andWhere(['<', 'id', $request->post('key')]);
            } else {
                $this->query->andWhere(['>', 'id', $request->post('key')]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        $query = clone $this->query;
        return (int)$query->limit(-1)->offset(-1)->orderBy([])->count('*', $this->db);
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        if ($this->formatter !== null) {
            $models = [];
            foreach ($query->all($this->db) as $index => $model) {
                $models[$index] = call_user_func($this->formatter, $model);
            }
            return $models;
        } else {
            return $query->all($this->db);
        }

    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return [
            'count' => 'totalCount',
            'keys',
            'models',
        ];
    }


}