<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心MODEL 查询语句构造器U 改
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Model\QueryBuilder;

use Library\Model\Crud\CrudModelInterface;

/**
 * Class UpdateQueryBuilder
 * @package Library\Model\QueryBuilder
 */
class UpdateQueryBuilder
{
    /**
     * @var CrudModelInterface
     */
    protected $model;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $conds;

    /**
     * @var string
     */
    protected $condsQuery;

    /**
     * @var array
     */
    protected $data;

    /**
     * @return CrudModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param CrudModelInterface $model
     *
     * @return UpdateQueryBuilder
     */
    public function setModel(CrudModelInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return UpdateQueryBuilder
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     *
     * @return UpdateQueryBuilder
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return array
     */
    public function getConds()
    {
        return $this->conds;
    }

    /**
     * @param array $conds
     *
     * @return UpdateQueryBuilder
     */
    public function setConds(array $conds)
    {
        $this->conds = $conds;

        return $this;
    }

    /**
     * @return string
     */
    public function getCondsQuery()
    {
        return $this->condsQuery;
    }

    /**
     * @param string $condsQuery
     *
     * @return UpdateQueryBuilder
     */
    public function setCondsQuery($condsQuery)
    {
        $this->condsQuery = $condsQuery;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->getModel() instanceof CrudModelInterface) {
            return $this->getModel()->toArray();
        }

        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return UpdateQueryBuilder
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
