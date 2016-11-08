<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器抽象类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Cache;

use Library\Cache\Exception\CacheException;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var int 生存时间
     */
    protected $ttl = 3600;

    /**
     * @var string 缓存前缀
     */
    protected $prefix = '';

    public function setOption($key, $value)
    {
        switch ($key) {
            //生存时间
            case 'ttl':
                $value = (int) $value;
                if ($value < 1) {
                    throw new CacheException("生存时间值不能小于1");
                }
                $this->ttl = $value;
                break;
            //前缀
            case 'prefix':
                $this->prefix = (string) $value;
                break;
            default:
                throw new CacheException('option not valid ' . $key);
                break;
        }

    }

    /**
     * 清洗所有的过期记录缓存
     */
    public function clearCache()
    {
        throw new CacheException('not ready yet');
    }

    /**
     * 清空所有缓存
     */
    public function dropCache()
    {
        throw new CacheException('not ready yet');
    }

    /**
     * 检查适配器是否工作
     *
     * @return boolean
     */
    public function check()
    {
        throw new CacheException('not ready yet');
    }

    /**
     * 获取缓存完整键名
     *
     * @return string
     */
    protected function getKey($key)
    {
        return sprintf('%s%s', $this->prefix, $key);
    }

    /**
     * 缓存打包
     *
     * @return string
     */
    protected function pack($value)
    {
        return serialize($value);
    }

    /**
     * 缓存解包
     *
     * @return mixed
     */
    protected function unPack($value)
    {
        return $value ? unserialize($value) : null;
    }
}
