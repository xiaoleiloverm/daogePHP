<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 APCu 用户变量缓存
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Construct\Cache\AbstractAdapter;

class Apcu extends AbstractAdapter
{
    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        return apc_delete($this->getKey($key));
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
        return $this->getValueFromCache($key);
    }

    /**
     * 清空所有缓存
     */
    public function dropCache()
    {
        apc_clear_cache('user'); //清除用户缓存
        return apc_clear_cache(); //清楚缓存
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
        $value = $this->getValueFromCache($key);
        if (is_null($value)) {
            return false;
        }
        return true;
    }

    /**
     * 设置缓存
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param int   $ttl 缓存生存周期 设置值为多少s后过期
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $res = apc_store(
            $this->getKey($key),
            $this->pack(
                [
                    'value' => $value,
                    'ttl'   => (int) $ttl + time(),
                ]
            ),
            $ttl
        );
        return $res ? true : false;
    }

    /**
     * 字段自增-用于记数
     * @param string $key  KEY值
     * @param int    $step 新增的step值
     */
    public function inc($key, $step)
    {
        return apc_inc($key, (int) $step);
    }

    /**
     * 字段自减-用于记数
     * @param string $key  KEY值
     * @param int    $step 新增的step值
     */
    public function dec($key, $step)
    {
        return apc_dec($key, (int) $step);
    }

    /**
     * 返回APC缓存信息
     */
    public function info()
    {
        return apc_cache_info();
    }

    /**
     * 获取缓存数据
     * @param string $key 键
     *
     * @return mixed|null
     */
    protected function getValueFromCache($key)
    {
        $data = $this->unPack(apc_fetch($this->getKey($key)));
        if (!$this->validateDataFromCache($data, $key)) {
            $this->del($key);

            return;
        }
        if ($this->ttlHasExpired($data['ttl'])) {
            $this->del($key);

            return;
        }

        return $data['value'];
    }

    /**
     * 格式化缓存数据
     * @param array $data 数据
     *
     * @return bool
     */
    protected function validateDataFromCache($data)
    {
        if (!is_array($data)) {
            return false;
        }
        foreach (['value', 'ttl'] as $missing) {
            if (!array_key_exists($missing, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 判断缓存是否过期
     * @param int $ttl 缓存过期时间戳
     *
     * @return bool
     */
    protected function ttlHasExpired($ttl)
    {
        return (time() > $ttl);
    }

}
