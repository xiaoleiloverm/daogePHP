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
    protected $dbh; //连接实例

    protected $driver; //驱动对象

    protected $driverName; //驱动名

    // 设置连接实例
    abstract protected function setDbh($dbh);

    //获取连接实例
    abstract protected function getDbh();

    //设置驱动
    protected function setDriver($driver)
    {
        if ($driver instanceof Db) {
            return $this->driver = $driver;
        }
        return;
    }

    //获取驱动
    protected function getDriver()
    {
        return $this->driver;
    }

    //设置驱动名
    protected function setDriverName($name)
    {
        return $this->driverName = $this->getDriverOption($name);
    }

    //获取驱动名
    protected function getDriverName()
    {
        return $this->driverName ?: (is_object($this->driver) ? get_class($this->driver) : null);
    }

    //驱动类型
    final protected function getDriverOption($driver)
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
    protected function beginTransaction()
    {
        return $this->driver->beginTransaction();
    }

    /**
     * 回滚
     * @return bool
     */
    protected function rollBack()
    {
        return $this->driver->rollBack();
    }

    /**
     * 提交一个事务
     * @return bool
     */
    protected function commit()
    {
        return $this->driver->commit();
    }

    /**
     * 设置连接实例 setDbh的别名
     * @return object
     */
    protected function connection($dbh)
    {
        //setDbh
        return $this->driver->setDbh();
    }

    /**
     * 关闭
     * @return void
     */
    protected function close()
    {
        $this->driver = null;
    }

}
