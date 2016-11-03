<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器接口类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Cache;

/**
 * 缓存适配器接口
 */
interface AdapterInterface
{

    /**
     * 检查适配器是否工作
     *
     * @return boolean
     */
    public function check();

    /**
     * 删除一条key对应的缓存
     *
     * @param string $key
     */
    public function del($key);

    /**
     * 获取缓存
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

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
