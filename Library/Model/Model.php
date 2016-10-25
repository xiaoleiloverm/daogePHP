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

class Model
{
    public $dbConn; //连接对象
    public function __construct()
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
        }
    }
}
