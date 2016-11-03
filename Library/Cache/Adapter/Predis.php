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

    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        $cmd = $this->predis->createCommand('DEL');
        $cmd->setArguments([$key]);

        $this->predis->executeCommand($cmd);
    }

    /**
     * 获取缓存
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key){
        $cmd = $this->predis->get($key);
    }

    /**
     * 缓存是否存在
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * 设置缓存
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param int    $ttl
     */
    public function set($key, $value, $ttl = null);

    /**
     * 设置适配器对应option 缓存周期和前缀
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value);

    /**
     * 清洗所有的过期记录缓存
     */
    public function clearCache();

    /**
     * 清空所有缓存
     */
    public function dropCache();
}
