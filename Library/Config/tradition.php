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

    /* 控制器设置 */
    'CONTROLLER_SUFFIX'     => 'Controller', //控制器类后缀
    'ACTION_SUFFIX'         => '', //控制器方法后缀

    /* Cookie设置 */

    /* SESSION设置 */

    /* 数据库设置 */
    'DB_TYPE'               => '', // 数据库类型
    'DB_HOST'               => '', // 服务器地址
    'DB_NAME'               => '', // 数据库名
    'DB_USER'               => '', // 用户名
    'DB_PWD'                => '', // 密码
    'DB_PORT'               => '', // 端口
    'UNIX_SOCKET'           => '', // unix套接字
    'DB_PREFIX'             => '', // 数据库表前缀
    'DB_DEBUG'              => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET'            => 'utf8', // 数据库编码默认采用utf8

    /* 数据缓存设置 */

    /* 错误设置 */
    'SHOW_ERROR_MESSAGE'    => true, //无错误页面是否显示错误信息
    'ERROR_PAGE'            => '', //错误定向页面
    'ERROR_MESSAGE'         => '页面错误！请稍后再试～', //错误显示信息,非调试模式有效

    /* 日志设置 */
    'LOG_TYPE'              => 'MonoLog', //日志类型 MonoLog、 SeasLog
    'LOG_SAVE_PATH'         => APP_PATH . 'Log/app.log', //日志生成文件,含路径

    /* URL配置 */
    'URL_MODEL'             => 1, //如果你的环境不支持PATHINFO 请设置为0 (0:动态url传参 模式;1:pathinfo 模式)
    'URL_HTML_SUFFIX'       => 'action', //url后缀
    'URL_PATHINFO_DEPR'     => '/', //PATHINFO URL分割符
    //子域名映射设置
    'SUB_DOMAIN_MAP_DEPLOY' => true, //true开 false关
    'SUB_DOMAIN_MAP'        => [],
    /* URL映射 '/x1[/x2[/x3]]'=>module/controller/action */
    'DOMAIN_URL_MAP'        => [
        //根据定义映射规则 可以自由定制简洁化的URL
        //当有配置子域名映射时 module会被替换成指定模块目录
        '/' => 'Home/Index/index', //前端
        //配置参考
        //'admin' => 'Admin/Product/index', //管理后台
        //'user/my'  => 'Home/User/index',//用户中心
        //'login'=>'Home/User/login',//登录界面
    ],
    //PATHINFO URL分割符

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
