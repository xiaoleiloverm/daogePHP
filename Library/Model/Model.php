<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心模型
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Model;

use Library\Model\Db;

class Model
{
    public $dbConn; //连接对象

    public $driver; //数据库驱动

    public $table; //数据表

    public $tablePrefix = ''; //数据表前缀

    /**
     * 架构函数
     * 取得数据库驱动的实例对象
     * @access public
     * @param string $table 模型名称
     * @param string $tablePrefix 表前缀
     * @param object $pdo 数据库连接pdo对象
     * @param string $driverName 数据库驱动名
     */
    public function __construct($table, $tablePrefix, $pdo, $driverName)
    {
        if ($this->dbConn) {
            return $this->dbConn;
        } else {
            //调用中间层
            try
            {
                $tablePrefix = !is_null($tablePrefix) ? ($this->tablePrefix = $tablePrefix) : C('DB_PREFIX');
                $this->table = $tablePrefix . $table;
                //设置驱动连接数据库实例
                $class        = '\\Library\\Model\\Driver\\' . Db::getDriverOption($driverName);
                $this->dbConn = Db::setDbh($pdo);
                $this->driver = Db::setDriver(new $class(Db::getDbh()));
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     *处理本类未定义函数,调用驱动方法
     */
    public function __call($name, $param_arr)
    {
        return call_user_func_array([$this->driver, $name], $param_arr);
    }

}
