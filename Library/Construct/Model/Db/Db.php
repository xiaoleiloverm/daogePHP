<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心数据库适配器抽象类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Model\Db;

abstract class Db
{
    //设置连接实例
    abstract public function setDbh(\PDO $dbh);

    //获取连接实例
    abstract public function getDbh();
}
