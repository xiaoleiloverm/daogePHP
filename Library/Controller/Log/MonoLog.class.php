<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> Monolog 1.1.* 日志类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

//use Library\Interface\Controller\Log\Log as LogInterface;

class MonoLog
{
    protected $logger;
    protected $handler;
    protected $channel;

    /*
     *日志等级
     */
    protected $levers = [
        'DEBUG'     => MonoLogger::DEBUG, //DEBUG (100): 详细的debug信息。
        'INFO'      => MonoLogger::INFO, //INFO (200): 关键事件。
        'NOTICE'    => MonoLogger::NOTICE, //NOTICE (250): 普通但是重要的事件。
        'WARNING'   => MonoLogger::WARNING, //WARNING (300): 出现非错误的异常。
        'ERROR'     => MonoLogger::ERROR, //ERROR (400): 运行时错误，但是不需要立刻处理。
        'CRITICAL'  => MonoLogger::CRITICAL, //CRITICA (500): 严重错误。
        'ALERT'     => MonoLogger::ALERT, //ALERT (550): 严重错误。
        'EMERGENCY' => MonoLogger::EMERGENCY, // EMERGENCY (600): 系统不可用。
    ];

    //初始化
    public function __construct($channel)
    {
        //初始化
        $this->logger = new MonoLogger($channel = 'daogePHP');
    }

    /**
     * EMERGENCY (600): 系统不可用。
     *
     * @param  string  $message 消息
     * @param  array  $context 上下文
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        return $this->record(__FUNCTION__, $message, $context);
    }

    /**
     * 警告
     *
     * @param  string  $message 消息
     * @param  array  $context 上下文
     * @return void
     */
    public function alert($message, array $context = [])
    {
        return $this->record(__FUNCTION__, $message, $context);
    }

    /**
     * CRITICA (500): 严重错误。
     *
     * @param  string  $message 消息
     * @param  array  $context 上下文
     * @return void
     */
    public function critical($message, array $context = [])
    {
        return $this->record(__FUNCTION__, $message, $context);
    }

    /**
     * ERROR (400): 运行时错误，但是不需要立刻处理。
     *
     * @param  string  $message 消息
     * @param  array  $context 上下文
     * @return void
     */
    public function error($message, array $context = [])
    {
        return $this->record(__FUNCTION__, $message, $context);
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
        $this->logger->{$level}($message, $context);
    }

    public function __call($name, $param)
    {
        throw new \BadFunctionCallException($name . "方法未定义"); //未定义的函数异常
    }

    /**
     * 创建日志文件
     * @param string $dir 文件路径
     * @param string $name 文件名
     * @return void
     */
    public function createLogFile($path = 'log/app.log', $level = 'debug')
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (APP_DEBUG) {
                trigger_error($dir . "不是一个目录", E_USER_WARNING); //警告
            }
        }
        // create a log channel
        $this->logger->pushHandler(new StreamHandler($path, $this->parseLevel(strtoupper($level))));
    }

    //level置换
    protected function parseLevel(string $level)
    {
        if (isset($this->levers[$level])) {
            return $this->levers[$level];
        }
        throw new \InvalidArgumentException("log level 未定义" . $level); //不是预期的类型异常
    }
}
