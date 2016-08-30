<?php
//----------------------------------
// daogePHP 核心惯例配置
//----------------------------------
/**
 *1.可在应用配置文件中设定和惯例不符的配置项
 *2.配置大小写任意，系统会自动转换为大写
 */
defined('DAOGE_PATH') or exit('');
return [

    /* Cookie设置 */

    /* SESSION设置 */

    /* 数据库设置 */

    /* 数据缓存设置 */

    /* 错误设置 */
    'ERROR_MESSAGE' => '页面错误！请稍后再试～', //错误显示信息,非调试模式有效

    /* 日志设置 */
    'LOG_TYPE'      => 'monoLog', //日志类型 monoLog、seasLog
    'LOG_SAVE_PATH' => APP_PATH . 'Log/app.log', //日志生成文件,含路径
    'LOG_HANDLER'   => '$error', //日志通道名(MonoLog专用),支持$name自定义命名
    'LOG_MESSAGE'   => '$message', //日志消息
];
