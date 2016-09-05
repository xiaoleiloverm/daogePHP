<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> seasLog日志伪代码类;seaslog需要seaslog扩展支持
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Controller\Log;

class SeasLog extends \SeasLog
{
    //常量列表
    /* SEASLOG_DEBUG                       "debug"
     * SEASLOG_INFO                        "info"
     * SEASLOG_NOTICE                      "notice"
     * SEASLOG_WARNING                     "warning"
     * SEASLOG_ERROR                       "error"
     * SEASLOG_CRITICAL                    "critical"
     * SEASLOG_ALERT                       "alert"
     * SEASLOG_EMERGENCY                   "emergency"
     */

    protected $logger; //日志对象
    protected $level; //日志等级

    /*
     *日志等级 php系统常量
     */
    protected $levers = [
        'DEBUG'     => SEASLOG_DEBUG, //DEBUG (100): 详细的debug信息。
        'INFO'      => SEASLOG_INFO, //INFO (200): 关键事件。
        'NOTICE'    => SEASLOG_NOTICE, //NOTICE (250): 普通但是重要的事件。
        'WARNING'   => SEASLOG_WARNING, //WARNING (300): 出现非错误的异常。
        'ERROR'     => SEASLOG_ERROR, //ERROR (400): 运行时错误，但是不需要立刻处理。
        'CRITICAL'  => SEASLOG_CRITICAL, //CRITICAL (500): 严重错误。
        'ALERT'     => SEASLOG_ALERT, //ALERT (550): 严重错误。
        'EMERGENCY' => SEASLOG_EMERGENCY, // EMERGENCY (600): 系统不可用。
    ];

    /**
     * 初始化
     *
     * @param  string  $channel 频道(日志处理器) 默认 daogePHP
     * @param  string  $level 日志等级 默认debug
     * @param  string  $createLogFile 创建日志文件 默认不创建
     * @return object 日志对象
     */
    public function __construct($level = 'debug', $channel = 'local', \SeasLog $handler = null)
    {
        //创建日志频道
        //$this->logger = new SeasLog($channel);
        //日志等级
        $level || $level = 'debug';
        $this->level     = $level;
        //创建文件
        if ($createLogFile != '') {
            //$this->createLogFile($createLogFile, $this->level);
        }
    }

    public function __destruct()
    {
        unset($logger, $level);
    }

    /**
     * 设置basePath
     *
     * @param $basePath
     *
     * @return bool
     */
    public static function setBasePath($basePath)
    {
        return parent::setBasePath($basePath);
    }

    /**
     * 获取basePath
     *
     * @return string
     */
    public static function getBasePath()
    {
        //return call_user_func_array([parent, __FUNCTION__], []);
        return parent::getBasePath();
    }

    /**
     * 设置模块目录
     * @param $module
     *
     * @return bool
     */
    public static function setLogger($module)
    {
        return parent::setLogger($module);
    }

    /**
     * 获取最后一次设置的模块目录
     * @return string
     */
    public static function getLastLogger()
    {
        return parent::getLastLogger();
    }

    /**
     * 设置DatetimeFormat配置
     * @param $format
     *
     * @return bool
     */
    public static function setDatetimeFormat($format)
    {
        return parent::setDatetimeFormat($format);
    }

    /**
     * 返回当前DatetimeFormat配置格式
     * @return string
     */
    public static function getDatetimeFormat()
    {
        return parent::getDatetimeFormat();
    }

    /**
     * 统计所有类型（或单个类型）行数
     * @param string $level
     * @param string $log_path
     * @param null   $key_word
     *
     * @return array | long
     */
    public static function analyzerCount($level = 'all', $log_path = '*', $key_word = null)
    {
        return parent::analyzerCount($level, $log_path, $key_word);
    }

    /**
     * 以数组形式，快速取出某类型log的各行详情
     *
     * @param        $level
     * @param string $log_path
     * @param null   $key_word
     * @param int    $start
     * @param int    $limit
     * @param        $order
     *
     * @return array
     */
    public static function analyzerDetail($level = SEASLOG_INFO, $log_path = '*', $key_word = null, $start = 1, $limit = 20, $order = SEASLOG_DETIAL_ORDER_ASC)
    {
        return parent::analyzerDetail($level, $log_path, $key_word, $start, $limit, $order);
    }

    /**
     * 获得当前日志buffer中的内容
     *
     * @return array
     */
    public static function getBuffer()
    {
        return parent::getBuffer();
    }

    /**
     * 将buffer中的日志立刻刷到硬盘
     *
     * @return bool
     */
    public static function flushBuffer()
    {
        return parent::flushBuffer();
    }

    /**
     * 记录debug日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function debug($message, array $content = array(), $module = '')
    {
        parent::debug($message, $content, $module);
    }

    /**
     * 记录info日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function info($message, array $content = array(), $module = '')
    {
        parent::info($message, $content, $module);
    }

    /**
     * 记录notice日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function notice($message, array $content = array(), $module = '')
    {
        parent::notice($message, $content, $module);
    }

    /**
     * 记录warning日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function warning($message, array $content = array(), $module = '')
    {
        parent::warning($message, $content, $module);
    }

    /**
     * 记录error日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function error($message, array $content = array(), $module = '')
    {
        parent::error($message, $content, $module);
    }

    /**
     * 记录critical日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function critical($message, array $content = array(), $module = '')
    {
        parent::critical($message, $content, $module);
    }

    /**
     * 记录alert日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function alert($message, array $content = array(), $module = '')
    {
        parent::alert($message, $content, $module);
    }

    /**
     * 记录emergency日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function emergency($message, array $content = array(), $module = '')
    {
        parent::emergency($message, $content, $module);
    }

    /**
     * 通用日志方法
     * @param        $level
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public static function log($level, $message, array $content = array(), $module = '')
    {
        ///
    }
}
