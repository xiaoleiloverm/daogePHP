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
    public static $dbh = null; //数据库连接实例对象

    public static $driver = 'mysql'; //数据库驱动

    /**
     * 设置数据库类实例
     * @static
     * @access public
     * @param Object 数据库实例
     * @return Object 数据库实例对象
     */
    public static function setDbh($dbh)
    {
        if ($dbh instanceof \PDO) {
            return static::$dbh = $dbh;
        }
        throw new \RuntimeException('dbh does not PDO Object.');
    }

    /**
     * 取得数据库类实例
     * @static
     * @access public
     * @param Object 数据库实例
     * @return Object 数据库实例对象
     */
    public static function getDbh()
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
    public static function getDriver()
    {
        return static::$driver;
    }

    /**
     * 设置数据库驱动
     * @return Object
     */
    public static function setDriver(DbAbstract $driver)
    {
        return static::$driver = $driver;
    }

    /**
     * 设置驱动数据库连接,设置数据库驱动和设置数据库类实例
     * @return Object
     */
    public static function setDriverDbh(DbAbstract $driver, $dbh)
    {
        self::setDbh($dbh);
        self::setDriver($driver);
        return static::$driver;
    }

    //驱动类型
    final public static function getDriverOption($driver)
    {
        switch ($driver = strtolower($driver)) {
            //Cubrid
            case 'cubrid':
                return 'cubrid';
                break;
            //FreeTDS / Microsoft SQL Server / Sybase
            case 'dblib':
                return 'dblib';
                break;
            //Firebird/Interbase 6
            case 'firebird':
                return 'firebird';
                break;
            //IBM DB2
            case 'ibm':
                return 'ibm';
                break;
            //IBM Informix Dynamic Server
            case 'informix':
                return 'informix';
                break;
            //MySQL 3.x/4.x/5.x
            case 'mysql':
                return 'mysql';
                break;
            //Oracle Call Interface
            case 'oci':
                return 'oci';
                break;
            //ODBC v3 (IBM DB2, unixODBC and win32 ODBC)
            case 'odbc':
                return 'odbc';
                break;
            //PostgreSQL
            case 'pgsql':
                return 'pgsql';
                break;
            //SQLite 3 及 SQLite 2
            case 'sqlite':
                return 'sqlite';
                break;
            //Microsoft SQL Server / SQL Azure
            case 'sqlsrv':
                return 'sqlsrv';
                break;
            //4D
            case '4d':
                return '4D';
                break;
            default:
                return 'mysql';
                break;
        }
    }

    /**
     * 启动一个事务
     * @return bool
     */
    public function beginTransaction()
    {
        return static::$driver->beginTransaction();
    }

    /**
     * 回滚
     * @return bool
     */
    public static function rollBack()
    {
        return static::$driver->rollBack();
    }

    /**
     * 提交一个事务
     * @return bool
     */
    public static function commit()
    {
        return static::$driver->commit();
    }

    /**
     * 关闭
     * @return void
     */
    public static function close()
    {
        static::$driver = null;
    }

    /**
     * 获取记录的行数
     * @return bool|int
     */
    public static function getRowCount()
    {
        return static::$driver->getRowCount();
    }

    /*
     * PDO执行一条 SQL 语句，并返回受影响的行数
     * @return int
     */
    public static function exec($sql)
    {
        return static::$driver->exec($sql);
    }

    /*
     * PDO执行一条SQL语句,返回一个结果集作为PDOStatement对象
     * @return array
     */
    public static function query($sql)
    {
        return static::$driver->query($sql);
    }

    /*
     * PDOStatement执行一条预处理语句
     * 执行预处理过的语句。如果预处理过的语句含有参数标记，必须选择下面其中一种做法:调用 PDOStatement::bindParam() 绑定 PHP 变量到参数标记
     * @param  sql 需要预处理的sql
     * @param  params 预处理参数
     * @return array
     */
    public static function execute($sql, $params = [])
    {
        return static::$driver->execute($sql, $params);
    }

    /*
     * 快捷执行一条 table 表的操作 如 DB::table('user')->select()
     * @return object
     */
    public static function table($table)
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
