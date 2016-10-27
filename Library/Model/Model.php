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

use Db;
use PDO;

class Model
{
    public $dbConn; //连接对象

    public $driver; //数据库驱动

    public function __construct($db, $host, $user, $password, $database, $fetchMode = \PDO::FETCH_ASSOC, $charset = 'utf8', array $options = [])
    {
        if ($this->dbConn) {
            return $this->dbConn;
        } else {
            // $confObj = new Config(DAOGE_PATH . '/App/Common/Conf');
            // $conf    = $confObj->offsetGet('config');
            // $config = $conf['DB_MASTER'];
            // $this->dbConn = new \Simplon\Mysql\Mysql($config['server'], $config['username'], $config['password'], $config['database']);
            // return $this->dbConn;

            //调用中间层
            try
            {
                //数据库
                $dns = $db ?: 'mysql';
                //主机
                $dns .= ':host=' . $host;
                //端口
                if (isset($options['port'])) {
                    $dns .= ';port=' . $options['port'];
                }
                // use unix socket
                if (isset($options['unixSocket'])) {
                    $dns = 'mysql:unix_socket=' . $options['unixSocket'];
                }
                //数据库名
                $dns .= ';dbname=' . $database;
                //编码
                $dns .= ';charset=' . $charset;

                //创建连接实例
                $pdo = new \PDO($dns, $user, $password);
                $this->dbConn = Db::setDbh($pdo);
                $_db          = Db::getDriverOption($db); //获取格式化驱动类
                $class        = '\\Library\\Model\\Driver\\' . $_db;
                //设置驱动
                $this->driver = Db::setDriver(new $class($this->dbConn));
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     *
     *获取DB驱动
     */
    protected function getDbDriver()
    {
        return Db::getDriver() ?: null;
    }

    /**
     *
     *获取连接实例
     */
    protected function getDbDriver()
    {
        return Db::getDbh() ?: null;
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
