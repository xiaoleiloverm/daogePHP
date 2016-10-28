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
use PDO;

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
     * 启动一个事务
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->driver->beginTransaction();
    }

    /**
     * 回滚
     * @return bool
     */
    protected static function rollBack()
    {
        return $this->driver->rollBack();
    }

    /**
     * 提交一个事务
     * @return bool
     */
    protected static function commit()
    {
        return $this->driver->commit();
    }

    /**
     * 关闭
     * @return void
     */
    protected static function close()
    {
        $this->driver = null;
    }

    /**
     * 获取记录的行数
     * @return bool|int
     */
    protected static function getRowCount()
    {
        return $this->driver->getRowCount();
    }

    /*
     * PDO执行一条 SQL 语句，并返回受影响的行数
     * @return int
     */
    protected static function exec($sql)
    {
        return $this->driver->exec($sql);
    }

    /*
     * PDO执行一条SQL语句,返回一个结果集作为PDOStatement对象
     * @return array
     */
    protected static function query($sql)
    {
        return $this->driver->query($sql);
    }

    /*
     * PDOStatement执行一条预处理语句
     * 执行预处理过的语句。如果预处理过的语句含有参数标记，必须选择下面其中一种做法:调用 PDOStatement::bindParam() 绑定 PHP 变量到参数标记
     * @return object
     */
    protected static function execute($sql)
    {
        return $this->driver->execute();
    }

}
