    <?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> seasLog日志类,必须安装并开启seaslog扩展
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

namespace Library\Controller\Log;

use Library\Interface\Controller\Log as LogInterface;

class SeasLog
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
    protected $handler = null;

    public function __construct()
    {
        #SeasLog init
        if (!class_exists('seaslog')) {
            throw new ErrorException("do not find php extend: seaslog");
            return;
        }
        //初始化
        $this->handler = new \SeasLog();
    }

    public function __call($name,$param_arr){
        return call_user_func_array([$this->handler, $name], $param_arr);
    }

    public function __destruct()
    {
        #SeasLog distroy
        unset($this->handler);
    }


    //---------------------------- 伪代码 ----------------------------//

     /**
     * 设置basePath
     *
     * @param $basePath
     *
     * @return bool
     */
    private function setBasePath($basePath)
    {
        return TRUE;
    }

    /**
     * 获取basePath
     *
     * @return string
     */
    private function getBasePath()
    {
        return 'the base_path';
    }

    /**
     * 设置模块目录
     * @param $module
     *
     * @return bool
     */
    private function setLogger($module)
    {
        return TRUE;
    }

    /**
     * 获取最后一次设置的模块目录
     * @return string
     */
    private function getLastLogger()
    {
        return 'the lastLogger';
    }

    /**
     * 设置DatetimeFormat配置
     * @param $format
     *
     * @return bool
     */
    private function setDatetimeFormat($format)
    {
        return TRUE;
    }

    /**
     * 返回当前DatetimeFormat配置格式
     * @return string
     */
    private function getDatetimeFormat()
    {
        return 'the datetimeFormat';
    }

    /**
     * 统计所有类型（或单个类型）行数
     * @param string $level
     * @param string $log_path
     * @param null   $key_word
     *
     * @return array | long
     */
    private function analyzerCount($level = 'all', $log_path = '*', $key_word = NULL)
    {
        return array();
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
    private function analyzerDetail($level = SEASLOG_INFO, $log_path = '*', $key_word = NULL, $start = 1, $limit = 20, $order = SEASLOG_DETIAL_ORDER_ASC)
    {
        return array();
    }

    /**
     * 获得当前日志buffer中的内容
     *
     * @return array
     */
    private function getBuffer()
    {
        return array();
    }

    /**
     * 将buffer中的日志立刻刷到硬盘
     *
     * @return bool
     */
    private function flushBuffer()
    {
        return TRUE;
    }

    /**
     * 记录debug日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function debug($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_DEBUG
    }

    /**
     * 记录info日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function info($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_INFO
    }

    /**
     * 记录notice日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function notice($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_NOTICE
    }

    /**
     * 记录warning日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function warning($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_WARNING
    }

    /**
     * 记录error日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function error($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_ERROR
    }

    /**
     * 记录critical日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function critical($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_CRITICAL
    }

    /**
     * 记录alert日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function alert($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_ALERT
    }

    /**
     * 记录emergency日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function emergency($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_EMERGENCY
    }

    /**
     * 通用日志方法
     * @param        $level
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    private function log($level, $message, array $content = array(), $module = '')
    {

    }
}