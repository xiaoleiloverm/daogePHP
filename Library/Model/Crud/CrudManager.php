<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心MODEL curd管理
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Model\Crud;

use Library\Construct\Model\Db\Db;
use Library\Model\Driver\MysqlException;
use Library\Model\Driver\MysqlQueryIterator;
use Library\Model\QueryBuilder\CreateQueryBuilder;
use Library\Model\QueryBuilder\DeleteQueryBuilder;
use Library\Model\QueryBuilder\ReadQueryBuilder;
use Library\Model\QueryBuilder\UpdateQueryBuilder;

/**
 * Class CrudManager
 * @package Library\Model\Crud
 */
class CrudManager
{
    /**
     * Db连接实例
     */
    protected $dbh;

    /**
     * @param Db $mysql
     */
    public function __construct(Db $dbh)
    {
        self::setDbh($dbh);
    }

    /**
     * @return dbh
     */
    private function setDbh($dbh)
    {
        return $this->dbh = $dbh;
    }

    /**
     * @param CreateQueryBuilder $builder
     *
     * @return CrudModelInterface
     * @throws MysqlException
     */
    public function create(CreateQueryBuilder $builder)
    {
        $builder->getModel()->beforeSave();

        $insertId = $this->getDbh()->insert(
            $builder->getTableName(),
            $builder->getData(),
            $builder->getInsertIgnore()
        );

        if ($insertId === false) {
            throw new MysqlException('Could not create dataset');
        }

        if (is_bool($insertId) !== true && method_exists($builder->getModel(), 'setId')) {
            $builder->getModel()->setId($insertId);
        }

        return $builder->getModel();
    }

    /**
     * @param ReadQueryBuilder $builder
     *
     * @return MysqlQueryIterator|null
     */
    public function read(ReadQueryBuilder $builder)
    {
        return $this
            ->getDbh()
            ->fetchRowManyCursor(
                $builder->renderQuery(),
                $builder->getConditions()
            );
    }

    /**
     * @param ReadQueryBuilder $builder
     *
     * @return array|null
     */
    public function readOne(ReadQueryBuilder $builder)
    {
        return $this
            ->getDbh()
            ->fetchRow(
                $builder->renderQuery(),
                $builder->getConditions()
            );
    }

    /**
     * @param UpdateQueryBuilder $builder
     *
     * @return CrudModelInterface
     * @throws MysqlException
     */
    public function update(UpdateQueryBuilder $builder)
    {
        $builder->getModel()->beforeUpdate();

        $condsQuery = null;

        if ($builder->getConds()) {
            $condsQuery = $this->buildCondsQuery($builder->getConds(), $builder->getCondsQuery());
        }

        $this->getDbh()->update(
            $builder->getTableName(),
            $builder->getConds(),
            $builder->getData(),
            $condsQuery
        );

        return $builder->getModel();
    }

    /**
     * @param DeleteQueryBuilder $builder
     *
     * @throws MysqlException
     */
    public function delete(DeleteQueryBuilder $builder)
    {
        $condsQuery = null;

        if ($builder->getConds()) {
            $condsQuery = $this->buildCondsQuery($builder->getConds(), $builder->getCondsQuery());
        }

        $this->getDbh()->delete(
            $builder->getTableName(),
            $builder->getConds(),
            $condsQuery
        );
    }

    /**
     * @return dbh
     */
    private function getDbh()
    {
        return $this->dbh;
    }

    /**
     * @param array $conds
     * @param string $condsQuery
     *
     * @return string
     */
    private function buildCondsQuery(array $conds, $condsQuery = null)
    {
        if ($condsQuery !== null) {
            return (string) $condsQuery;
        }

        $condsString = [];

        foreach ($conds as $key => $val) {
            $query = $key . ' = :' . $key;

            if (is_array($val) === true) {
                $query = $key . ' IN (:' . $key . ')';
            }

            $condsString[] = $query;
        }

        return join(' AND ', $condsString);
    }
}
