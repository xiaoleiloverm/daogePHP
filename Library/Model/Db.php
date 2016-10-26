<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心数据库适配器，中间层
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Model;

use Library\Construct\Model\Db\Db as DbAbstract;

class Db
{
    protected static $dbh = null; //数据库连接实例对象

    protected static $driver = 'mysql'; //数据库驱动

    /**
     * 取得数据库类实例
     * @static
     * @access protected
     * @param Object 数据库实例
     * @return Object 数据库实例对象
     */
    protected static function setDbh(DbAbstract $dbh)
    {
        if ($dbh instanceof \PDO) {
            return static::$dbh = $dbh;
        }
        throw new RuntimeException('dbh does not PDO Object.');
    }

    /**
     * 取得数据库类实例
     * @static
     * @access protected
     * @param Object 数据库实例
     * @return Object 数据库实例对象
     */
    protected static function getDbh()
    {
        if (isset(static::$dbh)) {
            return static::$dbh;
        }
        return null;
    }

    /**
     * 获取数据库驱动
     * @return Object 数据库驱动
     */
    protected static function getDriver()
    {
        return static::$driver;
    }

    /**
     * 设置数据库驱动
     * @return Object
     */
    protected static function setDriver(DbAbstract $dbh)
    {
        return static::$driver = $dbh::setDriver();
    }

    /**
     * 启动一个事务
     * @return bool
     */
    protected function beginTransaction()
    {
        return static::$driver->beginTransaction();
    }

    /**
     * 回滚
     * @return bool
     */
    protected static function rollBack()
    {
        return static::$driver->rollBack();
    }

    /**
     * 提交一个事务
     * @return bool
     */
    protected static function commit()
    {
        return static::$driver->commit();
    }

    /**
     * 设置连接实例 setDbh的别名
     * @return object
     */
    protected static function connection($dbh)
    {
        //setDbh
        return static::$driver->setDbh();
    }

    /**
     * 关闭
     * @return void
     */
    protected static function close()
    {
        static::$driver = null;
    }

    /**
     * 获取记录的行数
     * @return bool|int
     */
    protected static function getRowCount()
    {
        static::$driver->getRowCount();
    }

    /*
     * PDO执行一条 SQL 语句，并返回受影响的行数
     * @return int
     */
    protected static function exec($sql)
    {
        return static::$driver->exec($sql);
    }

    /*
     * PDO执行一条SQL语句,返回一个结果集作为PDOStatement对象
     * @return array
     */
    protected static function query($sql)
    {
        return static::$driver->query($sql);
    }

    /*
     * PDOStatement执行一条预处理语句
     * 执行预处理过的语句。如果预处理过的语句含有参数标记，必须选择下面其中一种做法:调用 PDOStatement::bindParam() 绑定 PHP 变量到参数标记
     * @return object
     */
    protected static function execute($sql)
    {
        return static::$driver->execute();
    }

    /*
     * 快捷执行一条 table 表的操作 如 DB::table('user')->select()
     * @return object
     */
    protected static function table($table)
    {
        //TODO
    }

    /*
     * 调用驱动类的方法
     * @return function
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array(array(static::$driver, $method), $params);
    }
}
