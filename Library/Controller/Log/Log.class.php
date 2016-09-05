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
namespace Library\Controller\Log;

use Library\Construct\Controller\Log\Log as LogConstruct;
use Library\Controller\Log\SeasLog;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use \Library\Controller\Log\MonoLog;

class Log extends LogConstruct
{
    protected $LogHandler = null;

    public function __construct($level = 'debug', $channel = 'local', LogConstruct $handler)
    {
        $logType = strtolower(C('LOG_TYPE')) ? strtolower(C('LOG_TYPE')) : 'monolog'; //记录日志类型
        if ($logType == 'monolog') {
            $this->LogHandler = new MonoLog($channel);
            //PDO handler
            //$logger->pushHandler(new PDOHandler(new PDO('sqlite:logs.sqlite'));
            //默认设置
            if ($handler != '' && is_object($handler)) {
                $handler = new StreamHandler(C('LOG_HANDLER') ? C('LOG_HANDLER') : APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
            }
            if ($handler != '' && !is_object($handler)) {
                throw new \InvalidArgumentException("handler不是一个对象"); //不是预期的类型异常
            }
            if ($handler instanceof StreamHandler) {
                $handler->setFormatter(new LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
            }
            $log = new MonoLog('local', $level, $handler);
        } else if ($logType == 'seaslog') {
            //SeasLog 需要php_SeasLog 扩展支持
            if (!class_exists('SeasLog')) {
                throw new ErrorException(L('_NOT_FIND_SEASLOG_'));
                return;
            }
            $this->LogHandler = new SeasLog($channel, $level, $handler);
        } else {
            $logType          = ucwords($logType);
            $obj              = '\Library\\Controller\\Log\\' . $logType;
            $this->LogHandler = new $obj($channel, $level, $handler);
        }
    }

    /**
     *处理本类未定义函数,对接扩展函数库
     */
    public function __call($name, $param_arr)
    {
        return call_user_func_array([$this->LogHandler, $name], $param_arr);
    }

    public function __destruct()
    {
        #SeasLog distroy
        unset($this->LogHandler);
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

}
