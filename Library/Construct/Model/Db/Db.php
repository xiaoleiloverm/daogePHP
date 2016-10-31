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
    public $dbh; //连接实例

    public $driver; //驱动对象

    public $driverName; //驱动名

    // 设置连接实例
    abstract protected function setDbh($dbh);

    //获取连接实例
    abstract public function getDbh();

    //设置驱动
    public function setDriver($driver)
    {
        if ($driver instanceof Db) {
            return $this->driver = $driver;
        }
        return;
    }

    //获取驱动
    public function getDriver()
    {
        return $this->driver;
    }

    //设置驱动名
    public function setDriverName($name)
    {
        return $this->driverName = $this->getDriverOption($name);
    }

    //获取驱动名
    public function getDriverName()
    {
        return $this->driverName ?: (is_object($this->driver) ? get_class($this->driver) : null);
    }

    //驱动类型
    final public function getDriverOption($driver)
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
        return $this->dbh->beginTransaction();
    }

    /**
     * 回滚
     * @return bool
     */
    public function rollBack()
    {
        return $this->dbh->rollBack();
    }

    /**
     * 提交一个事务
     * @return bool
     */
    public function commit()
    {
        return $this->dbh->commit();
    }

    /**
     * 设置连接实例 setDbh的别名
     * @return object
     */
    public function connection($dbh)
    {
        //setDbh
        return $this->dbh->setDbh();
    }

    /*
     * PDO执行一条 SQL 语句，并返回受影响的行数
     * @return int
     */
    public function exec($sql)
    {
        return $this->dbh->exec($sql);
    }

    /*
     * PDO执行一条SQL语句,返回一个结果集作为PDOStatement对象
     * @return array
     */
    public function query($sql)
    {
        return $this->dbh->query($sql);
    }

    /*
     * PDOStatement执行一条预处理语句
     * 执行预处理过的语句。如果预处理过的语句含有参数标记，必须选择下面其中一种做法:调用 PDOStatement::bindParam() 绑定 PHP 变量到参数标记
     * @param  sql 需要预处理的sql
     * @param  params 预处理参数
     * @return array
     */
    public function execute($sql, $params = [])
    {
        $this->dbh = $this->dbh->prepare($sql); //预处理
        if (is_string($params)) {
            $params = explode(',', $params);
        }
        return empty($params) ? $this->dbh->execute() : $this->dbh->execute($params);
    }

    /**
     * 关闭
     * @return void
     */
    public function close()
    {
        $this->driver = null;
        $this->dbh    = null;
    }

}
