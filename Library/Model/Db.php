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
    private static $connectDb = null; //数据库连接实例对象

    /**
     * 取得数据库类实例
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return Object 返回数据库驱动类
     */
    public static function getConnectDb(DbAbstract $dbh)
    {
        self::$connectDb = $dbh;
    }

    // 调用驱动类的方法
    public static function __callStatic($method, $params)
    {
        return call_user_func_array(array(self::$connectDb, $method), $params);
    }
}
