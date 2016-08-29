<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> log 日志驱动类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace library\Controller\Log;

class Log
{
    protected $handler = null;

    public function __construct()
    {
        //SeasLog 需要php_SeasLog 扩展支持
        if (!class_exists('seaslog')) {
            throw new ErrorException("do not find php extend: seaslog");
            return;
        }
        //初始化
        $this->handler = new \SeasLog();
    }

    /**
     *处理本类未定义函数,对接扩展函数库
     */
    public function __call($name, $param_arr)
    {
        return call_user_func_array([$this->handler, $name], $param_arr);
    }

    public function __destruct()
    {
        #SeasLog distroy
        unset($this->handler);
    }
}
