<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> Monolog 1.21.* 日志类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller\Log;

use Library\Construct\Controller\Log\Log as LogConstruct;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

class MonoLog extends LogConstruct
{
    protected $logger; //日志对象
    protected $level; //日志等级

    /*
     *日志等级
     */
    protected $levers = [
        'DEBUG'     => MonoLogger::DEBUG, //DEBUG (100): 详细的debug信息。
        'INFO'      => MonoLogger::INFO, //INFO (200): 关键事件。
        'NOTICE'    => MonoLogger::NOTICE, //NOTICE (250): 普通但是重要的事件。
        'WARNING'   => MonoLogger::WARNING, //WARNING (300): 出现非错误的异常。
        'ERROR'     => MonoLogger::ERROR, //ERROR (400): 运行时错误，但是不需要立刻处理。
        'CRITICAL'  => MonoLogger::CRITICAL, //CRITICAL (500): 严重错误。
        'ALERT'     => MonoLogger::ALERT, //ALERT (550): 严重错误。
        'EMERGENCY' => MonoLogger::EMERGENCY, // EMERGENCY (600): 系统不可用。
    ];

    /**
     * 初始化
     *
     * @param  string  $channel 频道(日志处理器) 默认 local
     * @param  string  $level 日志等级 默认debug
     * @param  string  $createLogFile 创建日志文件 默认不创建
     * @return object 日志对象
     */
    public function __construct($level = 'debug', $channel = 'local', LogConstruct $handler = null)
    {
        //创建日志频道
        $this->logger = new MonoLogger($channel);
        //日志等级
        $level || $level = 'debug';
        $this->level     = $level;
        //设置日志操作者
        if ($handler != '' && is_object($handler)) {
            //StreamHandler创建日志文件 new StreamHandler($path, $this->parseLevel(strtoupper($level)))
            $this->logger->pushHandler($handler);
        }
    }

    public function __destruct()
    {
        unset($logger, $level);
    }

    /*
     *设置日志操作者
     *
     * StreamHandler ：把记录写进PHP流，主要用于日志文件。
     * SyslogHandler ：把记录写进syslog。
     * ErrorLogHandler ：把记录写进PHP错误日志。
     * NativeMailerHandler ：使用PHP的 mail() 函数发送日志记录。
     * SocketHandler ：通过socket写日志。
     */
    //设置日志操作者
    private function setHander(LogConstruct $handler = null)
    {
        //设置日志操作者
        if ($handler != '' && is_object($handler)) {
            $this->logger->pushHandler($handler);
        }
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record(__FUNCTION__, $message, $context);
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
        return $this->record($level, $message, $context);
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
        return $this->record($level, $message, $context);
    }

    /**
     * 记录日志 通用日志记录方法
     * @param string $level 日志级别
     * @param string $level 通知消息
     * @param array $level 上下文
     * @return void
     */
    public function record($level, $message, $context)
    {
        $level || $level = $this->level;
        $level           = strtolower($level);
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
    public function parseLevel(string $level)
    {
        if (isset($this->levers[$level])) {
            return $this->levers[$level];
        }
        throw new \InvalidArgumentException("log level 未定义" . $level); //不是预期的类型异常
    }

    /**
     *默认文本格式化
     *
     * LineFormatter ：把日志记录格式化成一行字符串。
     * HtmlFormatter ：把日志记录格式化成HTML表格，主要用于邮件。
     * JsonFormatter ：把日志记录编码成JSON格式。
     * LogstashFormatter ：把日志记录格式化成logstash的事件JSON格式。
     * ElasticaFormatter ：把日志记录格式化成ElasticSearch使用的数据格式。
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }

}
