<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 Predis（纯php连接redis类）
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Construct\Cache\AbstractAdapter;

class Predis extends AbstractAdapter
{
    protected $predis; //redis连接实例

    /**
     * 构造方法 初始化缓存连接实例
     * @param  object client $client 缓存连接实例
     * @return void
     */
    public function __construct(client $client)
    {
        if ($client) {
            $this->predis = $client;
            return;
        }
        $this->predis = new client();
    }

    /**
     * 析构方法 释放连接
     * @param  void
     * @return void
     */
    public function __destruct()
    {
        $this->predis->disconnect();
    }
}
