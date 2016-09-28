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
use Library\Controller\Log\MonoLog;
use Library\Controller\Log\SeasLog;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Log
{
    protected $LogHandler = null;

    public function __construct($level = 'debug', $channel = 'local', LogConstruct $handler, $logType = null)
    {
        if ($logType == '') {
            $logType = strtolower(C('LOG_TYPE')) ? strtolower(C('LOG_TYPE')) : 'monolog'; //记录日志类型
        } else {
            $logType = strtolower($logType);
        }
        if ($logType == 'monolog') {
            $this->LogHandler = new MonoLog($channel);
            //PDO handler
            //$logger->pushHandler(new PDOHandler(new PDO('sqlite:logs.sqlite'));
            //默认设置
            if ($handler == '') {
                $handler = new StreamHandler((C('LOG_PATH') ? C('LOG_PATH') : APP_LOG_PATH) . 'app_' . date('Y-m-d', time()) . '.log');
            }
            if ($handler != '' && !is_object($handler)) {
                throw new \InvalidArgumentException("handler不是一个对象"); //不是预期的类型异常
            }
            if ($handler instanceof StreamHandler) {
                $handler->setFormatter(new LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
            }
            $this->LogHandler = new MonoLog($level, 'local', $handler);
        } else if ($logType == 'seaslog') {
            //SeasLog 需要php_SeasLog 扩展支持
            if (!class_exists('SeasLog')) {
                throw new ErrorException(L('_NOT_FIND_SEASLOG_'));
                return;
            }
            $this->LogHandler = new SeasLog($level, $channel, $handler);
        } else {
            $logType          = ucwords($logType);
            $obj              = '\\Library\\Controller\\Log\\' . $logType;
            $this->LogHandler = new $obj($level, $channel, $handler);
        }
    }

    /**
     *处理本类未定义函数,对接扩展函数库
     */
    public function __call($name, $param_arr)
    {
        return call_user_func_array([$this->LogHandler, $name], $param_arr);
    }

    /**
     *处理本类未定义静态函数,对接扩展函数库
     */
    public function __callStatic($name, $param_arr)
    {
        //静态方法调用$this的成员 需要 实例化获得
        return call_user_func_array([(new self)->LogHandler, $name], $param_arr);
    }

    /**
     * 记录日志 通用日志记录方法
     * SeasLog 和 MonoLog 的$message,$context 参数有区别
     * @param string $level 日志级别
     * @param string $level 通知消息
     * @param array $context ['extend'=>['扩展字符'],'replace'=>['{expample1}'=>'expample1']]
     * @param array $module 模块目录
     * @return void
     */
    public static function record($level, $message, $context = '', $module = '')
    {
        $_this           = (new self);
        $level || $level = $_this->LogHandler->level;
        $level           = strtolower($level);
        $extend          = ''; //扩展字符
        $replace         = []; //替换规则
        if (is_string($context)) {
            $extend = $context;
        } else if (is_array($context)) {
            isset($context['extend']) && $extend = $context['extend'];
            is_array($extend) && $extend         = json_encode($extend);
            if (isset($context['replace']) && is_array($context['replace'])) {
                $replace = $context['replace'];
            }
        }
        if (is_string($message)) {
            empty($replace) || $message = str_replace(array_keys($replace), array_values($replace), $message);
        }
        if (is_array($message)) {
            $tmp = '';
            foreach ($message as $key => &$value) {
                empty($replace) || $value = str_replace(array_keys($replace), array_values($replace), $value);
                $tmp .= (is_string($key) ? $key . ':' : '') . "{$value},";
            }
            $message = substr($tmp, 0, -1);
        }
        $extend && $message = $message . ' ' . $extend;
        $_this->LogHandler->{$level}($message, [], $module);
    }

    public function __destruct()
    {
        #Log distroy
        unset($this->LogHandler);
    }

}
