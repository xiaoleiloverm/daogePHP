<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 php Redis扩展
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Cache\Adapter\Exception\CacheException;
use Library\Construct\Cache\AbstractAdapter;

class Redis extends AbstractAdapter
{
    protected $redis; //redis连接实例

    /**
     * 构造方法 初始化缓存连接实例
     * @param  object Redis $Redis php扩展缓存连接实例
     * @return void
     */
    public function __construct(\Redis $redis, array $options = [])
    {
        if (!class_exists('Redis')) {
            throw new CacheException("uninstalled Redis extend"); //未安装redis扩展
        }
        //实例化扩展类
        $this->redis = $redis ?: (new \Redis());
        //构造连接实例
        $options = array_merge([
            'host'       => '127.0.0.1', //IP 或 UNIX DOMAIN SOCKET的路径
            'persistent' => false, //是否为长连接
        ], $options);
        $conn = $options['persistent'] ? 'pconnect' : 'connect';
        if ($options['port'] && $options['timeout']) {
            $this->redis->$conn($options['host'], $options['port'], $options['timeout']);
        } else if ($options['port']) {
            $this->redis->$conn($options['host'], $options['port']);
        } else {
            $this->redis->$conn($options['host']);
        }
    }

    /**
     * 析构方法 释放连接
     * @param  void
     * @return void
     */
    public function __destruct()
    {
        $this->redis = null;
    }

    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        $key = explode(',', $key);
        foreach ($key as &$v) {
            $v = $this->getKey($v);
        }
        //批量删除
        return $this->redis->delete($key);
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
        return $this->unPack($this->redis->get($this->getKey($key)));
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
        return $this->redis->exists($this->getKey($key));
    }

    /**
     * 设置缓存
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param int    $ttl 周期 s
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        return $this->redis->setex($this->getKey($key), $this->pack($value));
    }

    /**
     * 添加一个字符串值到LIST容器的顶部（左侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。
     *
     * @param string $key 键
     * @param mixed  $value 值
     */
    public function lPush($key, $value)
    {
        return $this->redis->lPush($this->getKey($key), $this->pack($value));
    }

    /**
     * 添加一个VALUE到LIST容器的顶部（左侧）如果这个LIST存在的话。
     *
     * @param string $key 键
     * @param mixed  $value 值
     */
    public function lPushx($key, $value)
    {
        return $this->redis->lPushx($this->getKey($key), $this->pack($value));
    }

    /**
     * 返回LIST底部（右侧）的VALUE，并且从LIST中把该VALUE弹出。
     *
     * @param string $key 键
     */
    public function rPop($key)
    {
        return $this->redis->rPop($this->getKey($key));
    }

    /**
     * 添加一个字符串值到LIST容器的顶部（右侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。
     *
     * @param string $key 键
     * @param mixed  $value 值
     */
    public function rPush($key, $value)
    {
        return $this->redis->rPush($this->getKey($key), $this->pack($value));
    }

    /**
     * 添加一个VALUE到LIST容器的底部（右侧）如果这个LIST存在的话。
     *
     * @param string $key 键
     * @param mixed  $value 值
     */
    public function rPushx($key, $value)
    {
        return $this->redis->rPushx($this->getKey($key), $this->pack($value));
    }

    /**
     * 返回LIST底部（左侧）的VALUE，并且从LIST中把该VALUE弹出。
     *
     * @param string $key 键
     */
    public function lPop($key)
    {
        return $this->redis->lPop($this->getKey($key));
    }

    /**
     * 返回LIST底部（左侧）的VALUE，并且从LIST中把该VALUE弹出。每次弹出都会阻塞$timeout秒时间。（阻塞方式）
     *
     * @param string $key 键
     * @param int $timeout 阻塞时间s
     */
    public function blPop($key, $timeout = 0)
    {
        $key = explode(',', $key);
        foreach ($key as &$v) {
            $v = $this->$prefix . $v;
        }
        return $this->redis->blPop($this->getKey($key), $timeout);
    }

    /**
     * 返回LIST底部（右侧）的VALUE，并且从LIST中把该VALUE弹出。每次弹出都会阻塞$timeout秒时间。（阻塞方式）
     *
     * @param string $key 键
     * @param int $timeout 阻塞时间s
     */
    public function brPop($key, $timeout = 0)
    {
        $key = explode(',', $key);
        foreach ($key as &$v) {
            $v = $this->$prefix . $v;
        }
        return $this->redis->brPop($this->getKey($key), $timeout);
    }

    /**
     * 缓存LIST长度
     *
     * @param string $key 键
     */
    public function lSize($key)
    {
        return $this->redis->lSize($this->getKey($key));
    }

    /**
     * 返回列表key中，下标为index的元素。
     *
     * @param string $key 键
     * @param int $index 下标(0为第一个元素，1为第二个元素。-1为倒数第一个元素，-2为倒数第二个元素)
     */
    public function lIndex($key, $index = 0)
    {
        return $this->redis->lIndex($this->getKey($key), $index);
    }

    /**
     * 获取LIST索引的值 0为第一个元素，1为第二个元素。-1为倒数第一个元素，-2为倒数第二个元素。如果指定了一个不存在的索引值，则返回FLASE。
     *
     * @param string $key 键
     * @param int $index 下标
     */
    public function lGet($key, $index = 0)
    {
        return $this->redis->lGet($this->getKey($key), $index);
    }

    /**
     * 指定LIST索引的值 如果设置成功返回TURE，如果KEY所指向的不是LIST，或者索引值超出LIST本身的长度范围，则返回flase。
     *
     * @param string $key 键
     * @param int $index 索引值
     */
    public function lSet($key, $index = 0)
    {
        return $this->redis->lSet($this->getKey($key), $index);
    }

    /**
     *处理本类未定义函数,对接Redis扩展库方法
     */
    public function __call($name, $param_arr)
    {
        !empty($param_arr[0]) && $param_arr[0] = $this->$prefix . $param_arr[0];
        return call_user_func_array([$this->redis, $name], $param_arr);
    }
}
