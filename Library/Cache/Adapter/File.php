<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 File 文件缓存
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Construct\Cache\AbstractAdapter;

class File extends AbstractAdapter
{
    protected $cache_file_prefix = '__'; //缓存文件前缀

    protected $cache_file_subfix = '.php.cache'; //缓存文件后缀

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * 构造方法 初始化文件缓存参数
     * @param  string $cacheDir 缓存文件路径
     * @param  string $cache_file_prefix 缓存文件前缀
     * @param  string $cache_file_subfix 缓存文件后缀
     * @return void
     */
    public function __construct($cacheDir = null, $cache_file_prefix = '__', $cache_file_subfix = '.php.cache')
    {
        if ($cache_file_prefix) {
            $this->cache_file_prefix = $cache_file_prefix;
        }
        if ($cache_file_subfix) {
            $this->cache_file_subfix = $cache_file_subfix;
        }
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()) . '/cache';
        }

        $this->cacheDir = (string) $cacheDir;

        $this->createCacheDirectory($cacheDir); //创建路径
    }

    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        $cacheFile = $this->getFileName($this->getKey($key)); //获取路径名
        $this->deleteFile($cacheFile);
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
    // public function dropCache()
    // {

    // }

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
        $cacheFile = $this->getFileName($this->getKey($key));
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $item = $this->pack(
            [
                'value' => $value,
                'ttl'   => (int) $ttl + time(),
            ]
        );
        if (!file_put_contents($cacheFile, $item)) {
            throw new CacheException(sprintf('Error saving data with the key "%s" to the cache file.', $key));
        }
    }

    /**
     * 创建文件路径
     * @param  string $cacheDir 缓存文件路径
     * @return void
     */
    protected function createCacheDirectory($path)
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new CacheException($path . ' is not writable');
            }
        }

        if (!is_writable($path)) {
            throw new CacheException($path . ' is not writable');
        }
    }

    /**
     * 删除文件
     * @param  string $cacheFile 文件名(包含路径)
     * @return bool
     */
    protected function deleteFile($cacheFile)
    {
        if (is_file($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
    }

    /**
     * 获取文件名
     * @param  string $key 缓存键
     * @return string 文件名(包含路径)
     */
    protected function getFileName($key)
    {
        return $this->cacheDir .
        DIRECTORY_SEPARATOR .
        $this->cache_file_prefix .
        $this->getKey($key) .
        $this->cache_file_subfix;
    }

    /**
     * 获取缓存数据
     * @param string $key 键
     *
     * @return mixed|null
     */
    protected function getValueFromCache($key)
    {
        $path = $this->getFileName($this->getKey($key));

        if (!file_exists($path)) {
            return;
        }

        $data = $this->unPack(file_get_contents($path));
        if (!$data || !$this->validateDataFromCache($data) || $this->ttlHasExpired($data['ttl'])) {
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
