<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 memcache(memcache原生实现,memcaced是libmemcached实现 只支持oo接口)
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Construct\Cache\AbstractAdapter;
use Memcache as BaseMemcache;

class Memcache extends AbstractAdapter
{
    protected $server; //连接实例

    /**
     * 构造方法 初始化缓存连接实例
     * @param  object client $client 缓存连接实例
     * @return void
     */
    public function __construct(BaseMemcache $server)
    {
        if ($server) {
            $this->server = $server;
            return;
        }
        $this->server = new BaseMemcache();
        //bool Memcache::addServer ( string $host [, int $port [, bool $persistent [, int $weight [, int $timeout [, int $retry_interval [, bool $status [, callback $failure_callback ]]]]]]] )
        $this->server->addServer('localhost', 11211);
    }

    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        return $this->server->delete($this->getKey($key));
    }

    /**
     * 获取缓存
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->server->get($this->getKey($key));
        return $data ? $this->unPack($data) : '';
    }

    /**
     * 缓存是否存在
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->server->get($this->getKey($key)) ? true : false;
    }

    /**
     * 设置缓存
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param bool   $flag：是否用MEMCACHE_COMPRESSED来压缩存储的值，true表示压缩，false表示不压缩。
     * @param int   $ttl 存储值的过期时间，如果为0表示不会过期
     *                   你可以用unix时间戳或者描述来表示从现在开始的时间，但是你在使用秒数表示的时候，不要超过2592000秒 (表示30天)。
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        return $this->server->set($this->getKey($key), $this->pack($value), false, $ttl);
    }

}
