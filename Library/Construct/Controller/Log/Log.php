<?php
namespace Library\Construct\Controller\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class Log implements PsrLoggerInterface
{
    //写任意日志抽象方法
    abstract public function write($level, $message, array $context = []);

    /**
     * 记录debug日志
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    public function debug($message, array $content = array(), $module = '')
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
    public function info($message, array $content = array(), $module = '')
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
    public function notice($message, array $content = array(), $module = '')
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
    public function warning($message, array $content = array(), $module = '')
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
    public function error($message, array $content = array(), $module = '')
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
    public function critical($message, array $content = array(), $module = '')
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
    public function alert($message, array $content = array(), $module = '')
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
    public function emergency($message, array $content = array(), $module = '')
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
    public function log($level, $message, array $content = array(), $module = '')
    {

    }
}
