<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心MODEL MysqlQuery迭代器
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Model\Driver;

class MysqlQueryIterator implements \Iterator
{
    protected $position;
    protected $pdoStatement;
    protected $fetchType;
    protected $fetchMode;
    protected $data;

    /**
     * @param \PDOStatement $pdoStatement
     * @param $fetchType
     * @param int $fetchMode
     */
    public function __construct(\PDOStatement $pdoStatement, $fetchType, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $this->position     = 0;
        $this->pdoStatement = $pdoStatement;
        $this->fetchType    = $fetchType;
        $this->fetchMode    = $fetchMode;
    }

    public function rewind()
    {
        $this->position = 0;
        $this->data     = $this->fetchType === 'fetch' ? $this->pdoStatement->fetch($this->fetchMode) : $this->pdoStatement->fetchColumn();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->data;
    }

    /**
     * @return int|mixed
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->data = $this->fetchType === 'fetch' ? $this->pdoStatement->fetch($this->fetchMode) : $this->pdoStatement->fetchColumn();
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->data !== false;
    }
}
