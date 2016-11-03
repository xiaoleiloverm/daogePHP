<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Cache;

use \Library\Construct\Cache\AdapterInterface;
use \Library\Construct\Cache\CacheInterface;
use \Library\Construct\Cache\Exception\AdapterNotSetException;

class Cache implements CacheInterface
{
    protected $adapter; //缓存操作对象

    /**
     * 构造方法 缓存操作对象依赖注入
     * @param  object $adapter 缓存对象
     * @return void
     */
    public function __construct(AdapterInterface $adapter)
    {
        if ($adapter) {
            $this->setAdapter($adapter); //设置缓存操作对象
        }
    }

    /**
     * 设置缓存对象
     * @param  object AdapterInterface $adapter 缓存对象
     * @return void
     */
    protected function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * 获取缓存对象
     * @param  void
     * @return object AdapterInterface $adapter 缓存对象
     */
    protected function getAdapter()
    {
        if (!$this->adapter) {
            throw new AdapterNotSetException('Required Adapter');
        }
        return $this->adapter;
    }

    /**
     * 删除缓存
     * @param  string $key 键
     * @return object AdapterInterface $adapter 缓存对象
     */
    public function delete($key)
    {
        $this->getAdapter()->del($key);
    }

    /**
     * 获取缓存
     * @param  string $key 键
     * @return object AdapterInterface $adapter 缓存对象
     */
    public function get($key)
    {
        $this->getAdapter()->get($key);
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
        $this->getAdapter()->has($key);
    }

    /**
     * 设置缓存
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param int    $ttl
     */
    public function set($key, $value, $ttl = null)
    {
        $this->getAdapter()->set($key, $value, $ttl);
    }

    /**
     * 设置适配器对应option
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value)
    {
        $this->getAdapter()->setOption($key, $value);
    }

    /**
     * 清洗所有的过期记录缓存
     */
    public function clearCache()
    {
        $this->getAdapter()->clearCache();
    }

    /**
     * 清空所有缓存
     */
    public function dropCache()
    {
        $this->getAdapter()->dropCache();
    }

}
