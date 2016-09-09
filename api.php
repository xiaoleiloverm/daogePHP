<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： API 初始化文件 需要swoole_php扩展支持
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */

//系统常量配置
defined('API_DIR') or define('API_DIR', APP_PATH . 'Api' . '/'); //API目录
defined('API_PORT') or define('API_PORT', 9502); //端口常量配置
defined('API_SERVER_IP') or define('API_SERVER_IP', '0.0.0.0'); //http_server IP
defined('API_WORKER_NUM') or define('API_WORKER_NUM', 5); //工作进程数量
defined('API_DEAMONIZE') or define('API_DEAMONIZE', true); //是否作为守护进程

//启动http server
if (!class_exists(swoole_http_server)) {
    die('not found swoole_php extend');
}
$http = new swoole_http_server(API_SERVER_IP, API_PORT);
$http->set(array(
    'worker_num' => API_WORKER_NUM, //工作进程数量
    'daemonize'  => API_DEAMONIZE, //是否作为守护进程
));
//加载应用核心类文件
require_once LIB_PATH . 'Core.class.php';
//初始化
\Library\Core::init();
//加载应用配置文件
\Library\Core::appConfig();
$http->on('request', function ($request, $response) {
    // 阻止google浏览器的ico请求
    if ($request->server['request_uri'] == '/favicon.ico') {
        $response->end();exit;
    }
    global $globalRes;
    $globalRes = $response;
    $_SERVER   = $request->server;
    //路由调度
    $res = \Library\Core::http_C(); //加载http C层
    $response->end($res);
});
$http->start();
