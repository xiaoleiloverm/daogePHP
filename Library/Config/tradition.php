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
    /* 系统设置 */
    'DEFAULT_TIMEZONE'      => 'PRC', // 默认时区

    /* Cookie设置 */

    /* SESSION设置 */

    /* 数据库设置 */

    /* 数据缓存设置 */

    /* 错误设置 */
    'SHOW_ERROR_MESSAGE'    => true, //无错误页面是否显示错误信息
    'ERROR_PAGE'            => '', //错误定向页面
    'ERROR_MESSAGE'         => '页面错误！请稍后再试～', //错误显示信息,非调试模式有效

    /* 日志设置 */
    'LOG_TYPE'              => 'MonoLog', //日志类型 MonoLog、SeasLog
    'LOG_SAVE_PATH'         => APP_PATH . 'Log/app.log', //日志生成文件,含路径
    'LOG_HANDLER'           => '$error', //日志通道名(MonoLog专用),支持$name自定义命名
    'LOG_MESSAGE'           => '$message', //日志消息

    /* 默认设定 */
    'DEFAULT_M_NAME'        => 'Model', //模型层命名
    'DEFAULT_V_NAME'        => 'View', //视图层命名
    'DEFAULT_C_NAME'        => 'Controller', //控制器层命名
    'DEFAULT_HTTP_NAME'     => 'App', //HTTP层命名
    'DEFAULT_SERVER_NAME'   => 'Server', //服务层命名
    'DEFAULT_SOAP_NAME'     => 'RPC', //SOAP层命名
    'DEFAULT_LANG'          => 'zh-cn', //简体中文
    'DEFAULT_MODULE'        => 'Home', // 默认模块
    'DEFAULT_CONTROLLER'    => 'Index', // 默认控制器名称
    'DEFAULT_ACTION'        => 'index', // 默认操作名称
    'DEFAULT_CHARSET'       => 'utf-8', // 默认输出编码
    'DEFAULT_AJAX_RETURN'   => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_JSONP_HANDLER' => 'jsonpReturn', // 默认JSONP格式返回的处理方法
    'DEFAULT_FILTER'        => 'htmlspecialchars', // 默认参数过滤方法 用于I函数...

];
