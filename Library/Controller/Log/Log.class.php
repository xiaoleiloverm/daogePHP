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

    /**
     * EMERGENCY (600): 系统不可用。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function emergency($message, array $context = [])
    {

    }

    /**
     * 警告
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function alert($message, array $context = [])
    {

    }

    /**
     * CRITICAL (500): 严重错误。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function critical($message, array $context = [])
    {

    }

    /**
     * ERROR (400): 运行时错误，但是不需要立刻处理。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function error($message, array $context = [])
    {

    }

    /**
     * WARNING (300): 出现非错误的异常。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function warning($message, array $context = [])
    {

    }

    /**
     * NOTICE (250): 普通但是重要的事件。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function notice($message, array $context = [])
    {

    }

    /**
     * INFO (200): 关键事件。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function info($message, array $context = [])
    {

    }

    /**
     * DEBUG (100): 详细的debug信息。
     *
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function debug($message, array $context = [])
    {

    }

    /**
     * 任意级别
     *
     * @param  string  $level 级别
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function log($level, $message, array $context = [])
    {

    }

    /**
     * 任意级别
     *
     * @param  string  $level 级别
     * @param  string  $message 消息(日志抬头)
     * @param  array  $context 内容
     * @return void
     */
    public function write($level, $message, array $context = [])
    {

    }

    /**
     * 记录日志
     * @param string $level 日志级别
     * @param string $level 通知消息
     * @param array $level 上下文
     * @return void
     */
    public function record($level, $message, $context)
    {

    }

}
