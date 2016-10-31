<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心数据库驱动，mysql
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Model\Driver;

use Library\Construct\Model\Db\Db as DbAbstract;

class Mysql extends DbAbstract
{
    public $dbh; //连接对象

    public function __construct($dbh)
    {
        $this->setDbh($dbh);
    }

    // 设置连接实例
    public function setDbh($dbh)
    {
        return $this->dbh = $dbh;
    }

    //获取连接实例
    public function getDbh()
    {
        return $this->dbh;
    }

    public function execute($sql, $params = [])
    {
        return parent::execute($sql, $params);
    }

    public function clearLastStatement()
    {
        $this->lastStatement = null;
    }
}
