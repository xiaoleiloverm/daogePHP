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

// use \Library\Construct\Cache\Exception\AdapterNotSetException;
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
        return $this->adapter ?: null;
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

}
