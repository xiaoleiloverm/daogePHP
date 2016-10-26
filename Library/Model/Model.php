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

use PDO;
use Db;

class Model
{
    public $dbConn; //连接对象
    public function __construct($host, $user, $password, $database, $fetchMode = \PDO::FETCH_ASSOC, $charset = 'utf8', array $options = [])
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
                // use host
                $dns = 'mysql:host=' . $host;

                if (isset($options['port'])) {
                    $dns .= ';port=' . $options['port'];
                }

                // use unix socket
                if (isset($options['unixSocket'])) {
                    $dns = 'mysql:unix_socket=' . $options['unixSocket'];
                }

                $dns .= ';dbname=' . $database;
                $dns .= ';charset=' . $charset;

                // ------------------------------

                // create PDO instance
                //TODO
                $this->dbConn = new \PDO($dns, $user, $password));
                Db::setDbh($this->dbConn);

                // set fetchMode
                $this->setFetchMode($fetchMode);
            } catch (\PDOException $e) {
                throw new MysqlException($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     *
     *获取DB驱动
     */
    protected function getDbDriver()
    {

    }

    /**
     * @param int $fetchMode
     *
     * @return Db
     */
    protected function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * @return int
     */
    protected function getFetchMode()
    {
        return (int)$this->fetchMode;
    }
}
