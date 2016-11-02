<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存接口类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Cache;

use \Library\Construct\Cache\AdapterInterface;

/**
 *缓存接口
 */
interface CacheInterface
{
    /**
     * 删除缓存
     * @param  $key 缓存key
     * @return array
     */
    public function delete($key);

    /**
     * 获取缓存
     * @param  $key 缓存key
     * @return array
     */
    public function get($key);

    /**
     * 获取适配器
     * @return AdapterInterface $adapter
     * @throws Exception
     */
    public function getAdapter();

    /**
     * 检索value对应的key是否存在
     *
     * @param string $key
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
     * 设置缓存适配器
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter);

    /**
     * 设置适配器对应option
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
